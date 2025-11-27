from fastapi import FastAPI, APIRouter, HTTPException, UploadFile, File, Form
from fastapi.staticfiles import StaticFiles
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field, ConfigDict
from typing import List, Optional
import uuid
from datetime import datetime, timezone
import shutil

ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

# Create uploads directory
UPLOADS_DIR = ROOT_DIR / 'uploads'
UPLOADS_DIR.mkdir(exist_ok=True)

# MongoDB connection
mongo_url = os.environ['MONGO_URL']
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ['DB_NAME']]

# Create the main app without a prefix
app = FastAPI()

# Serve static files
app.mount("/uploads", StaticFiles(directory=str(UPLOADS_DIR)), name="uploads")

# Create a router with the /api prefix
api_router = APIRouter(prefix="/api")

# ============ MODELS ============

class Category(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    slug: str
    description: Optional[str] = None
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class CategoryCreate(BaseModel):
    name: str
    slug: str
    description: Optional[str] = None

class Article(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    title: str
    slug: str
    content: str
    excerpt: str
    category_id: str
    author: str
    featured_image: Optional[str] = None
    status: str = "published"  # draft, published
    meta_description: Optional[str] = None
    tags: List[str] = []
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))
    updated_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class ArticleCreate(BaseModel):
    title: str
    slug: str
    content: str
    excerpt: str
    category_id: str
    author: str
    featured_image: Optional[str] = None
    status: str = "published"
    meta_description: Optional[str] = None
    tags: List[str] = []

class CasinoListing(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    rank: int
    logo_url: str
    offer_title: str
    offer_details: str
    features: List[str]
    promo_code: Optional[str] = None
    review_link: Optional[str] = None
    claim_link: str
    rating: float = 5.0
    is_featured: bool = False
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class CasinoListingCreate(BaseModel):
    name: str
    rank: int
    logo_url: str
    offer_title: str
    offer_details: str
    features: List[str]
    promo_code: Optional[str] = None
    review_link: Optional[str] = None
    claim_link: str
    rating: float = 5.0
    is_featured: bool = False

# ============ ROUTES ============

@api_router.get("/")
async def root():
    return {"message": "Gaming Today API"}

# ========= CATEGORIES =========

@api_router.post("/categories", response_model=Category)
async def create_category(input: CategoryCreate):
    category = Category(**input.model_dump())
    doc = category.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    await db.categories.insert_one(doc)
    return category

@api_router.get("/categories", response_model=List[Category])
async def get_categories():
    categories = await db.categories.find({}, {"_id": 0}).to_list(1000)
    for cat in categories:
        if isinstance(cat['created_at'], str):
            cat['created_at'] = datetime.fromisoformat(cat['created_at'])
    return categories

@api_router.get("/categories/{category_id}", response_model=Category)
async def get_category(category_id: str):
    category = await db.categories.find_one({"id": category_id}, {"_id": 0})
    if not category:
        raise HTTPException(status_code=404, detail="Category not found")
    if isinstance(category['created_at'], str):
        category['created_at'] = datetime.fromisoformat(category['created_at'])
    return category

@api_router.put("/categories/{category_id}", response_model=Category)
async def update_category(category_id: str, input: CategoryCreate):
    existing = await db.categories.find_one({"id": category_id})
    if not existing:
        raise HTTPException(status_code=404, detail="Category not found")
    
    update_data = input.model_dump()
    await db.categories.update_one({"id": category_id}, {"$set": update_data})
    
    updated = await db.categories.find_one({"id": category_id}, {"_id": 0})
    if isinstance(updated['created_at'], str):
        updated['created_at'] = datetime.fromisoformat(updated['created_at'])
    return Category(**updated)

@api_router.delete("/categories/{category_id}")
async def delete_category(category_id: str):
    result = await db.categories.delete_one({"id": category_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Category not found")
    return {"message": "Category deleted successfully"}

# ========= ARTICLES =========

@api_router.post("/articles", response_model=Article)
async def create_article(input: ArticleCreate):
    article = Article(**input.model_dump())
    doc = article.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    doc['updated_at'] = doc['updated_at'].isoformat()
    await db.articles.insert_one(doc)
    return article

@api_router.get("/articles", response_model=List[Article])
async def get_articles(category_id: Optional[str] = None, status: Optional[str] = None, limit: int = 100):
    query = {}
    if category_id:
        query['category_id'] = category_id
    if status:
        query['status'] = status
    
    articles = await db.articles.find(query, {"_id": 0}).sort("created_at", -1).to_list(limit)
    for article in articles:
        if isinstance(article['created_at'], str):
            article['created_at'] = datetime.fromisoformat(article['created_at'])
        if isinstance(article['updated_at'], str):
            article['updated_at'] = datetime.fromisoformat(article['updated_at'])
    return articles

@api_router.get("/articles/{article_id}", response_model=Article)
async def get_article(article_id: str):
    article = await db.articles.find_one({"id": article_id}, {"_id": 0})
    if not article:
        raise HTTPException(status_code=404, detail="Article not found")
    if isinstance(article['created_at'], str):
        article['created_at'] = datetime.fromisoformat(article['created_at'])
    if isinstance(article['updated_at'], str):
        article['updated_at'] = datetime.fromisoformat(article['updated_at'])
    return article

@api_router.get("/articles/slug/{slug}", response_model=Article)
async def get_article_by_slug(slug: str):
    article = await db.articles.find_one({"slug": slug}, {"_id": 0})
    if not article:
        raise HTTPException(status_code=404, detail="Article not found")
    if isinstance(article['created_at'], str):
        article['created_at'] = datetime.fromisoformat(article['created_at'])
    if isinstance(article['updated_at'], str):
        article['updated_at'] = datetime.fromisoformat(article['updated_at'])
    return article

@api_router.put("/articles/{article_id}", response_model=Article)
async def update_article(article_id: str, input: ArticleCreate):
    existing = await db.articles.find_one({"id": article_id})
    if not existing:
        raise HTTPException(status_code=404, detail="Article not found")
    
    update_data = input.model_dump()
    update_data['updated_at'] = datetime.now(timezone.utc).isoformat()
    await db.articles.update_one({"id": article_id}, {"$set": update_data})
    
    updated = await db.articles.find_one({"id": article_id}, {"_id": 0})
    if isinstance(updated['created_at'], str):
        updated['created_at'] = datetime.fromisoformat(updated['created_at'])
    if isinstance(updated['updated_at'], str):
        updated['updated_at'] = datetime.fromisoformat(updated['updated_at'])
    return Article(**updated)

@api_router.delete("/articles/{article_id}")
async def delete_article(article_id: str):
    result = await db.articles.delete_one({"id": article_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Article not found")
    return {"message": "Article deleted successfully"}

# ========= CASINO LISTINGS =========

@api_router.post("/casinos", response_model=CasinoListing)
async def create_casino(input: CasinoListingCreate):
    casino = CasinoListing(**input.model_dump())
    doc = casino.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    await db.casinos.insert_one(doc)
    return casino

@api_router.get("/casinos", response_model=List[CasinoListing])
async def get_casinos(featured: Optional[bool] = None):
    query = {}
    if featured is not None:
        query['is_featured'] = featured
    
    casinos = await db.casinos.find(query, {"_id": 0}).sort("rank", 1).to_list(1000)
    for casino in casinos:
        if isinstance(casino['created_at'], str):
            casino['created_at'] = datetime.fromisoformat(casino['created_at'])
    return casinos

@api_router.get("/casinos/{casino_id}", response_model=CasinoListing)
async def get_casino(casino_id: str):
    casino = await db.casinos.find_one({"id": casino_id}, {"_id": 0})
    if not casino:
        raise HTTPException(status_code=404, detail="Casino not found")
    if isinstance(casino['created_at'], str):
        casino['created_at'] = datetime.fromisoformat(casino['created_at'])
    return casino

@api_router.put("/casinos/{casino_id}", response_model=CasinoListing)
async def update_casino(casino_id: str, input: CasinoListingCreate):
    existing = await db.casinos.find_one({"id": casino_id})
    if not existing:
        raise HTTPException(status_code=404, detail="Casino not found")
    
    update_data = input.model_dump()
    await db.casinos.update_one({"id": casino_id}, {"$set": update_data})
    
    updated = await db.casinos.find_one({"id": casino_id}, {"_id": 0})
    if isinstance(updated['created_at'], str):
        updated['created_at'] = datetime.fromisoformat(updated['created_at'])
    return CasinoListing(**updated)

@api_router.delete("/casinos/{casino_id}")
async def delete_casino(casino_id: str):
    result = await db.casinos.delete_one({"id": casino_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Casino not found")
    return {"message": "Casino deleted successfully"}

# ========= IMAGE UPLOAD =========

@api_router.post("/upload")
async def upload_image(file: UploadFile = File(...)):
    try:
        # Generate unique filename
        file_extension = file.filename.split('.')[-1]
        unique_filename = f"{uuid.uuid4()}.{file_extension}"
        file_path = UPLOADS_DIR / unique_filename
        
        # Save file
        with open(file_path, "wb") as buffer:
            shutil.copyfileobj(file.file, buffer)
        
        # Return URL
        file_url = f"/uploads/{unique_filename}"
        return {"url": file_url}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# Include the router in the main app
app.include_router(api_router)

app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=os.environ.get('CORS_ORIGINS', '*').split(','),
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)

@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()