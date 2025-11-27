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

class UserReview(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    casino_id: str
    user_name: str
    rating: float
    title: str
    comment: str
    pros: List[str] = []
    cons: List[str] = []
    is_verified: bool = False
    status: str = "pending"  # pending, approved, rejected
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class UserReviewCreate(BaseModel):
    casino_id: str
    user_name: str
    rating: float
    title: str
    comment: str
    pros: List[str] = []
    cons: List[str] = []

class AffiliateLink(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    casino_id: Optional[str] = None
    url: str
    description: Optional[str] = None
    clicks: int = 0
    is_active: bool = True
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class AffiliateLinkCreate(BaseModel):
    name: str
    casino_id: Optional[str] = None
    url: str
    description: Optional[str] = None
    is_active: bool = True

class Advertisement(BaseModel):
    model_config = ConfigDict(extra="ignore")
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    position: str  # header, sidebar, footer, in-content
    image_url: str
    link_url: str
    alt_text: str
    is_active: bool = True
    impressions: int = 0
    clicks: int = 0
    start_date: Optional[datetime] = None
    end_date: Optional[datetime] = None
    created_at: datetime = Field(default_factory=lambda: datetime.now(timezone.utc))

class AdvertisementCreate(BaseModel):
    name: str
    position: str
    image_url: str
    link_url: str
    alt_text: str
    is_active: bool = True
    start_date: Optional[datetime] = None
    end_date: Optional[datetime] = None

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

# ========= USER REVIEWS =========

@api_router.post("/reviews", response_model=UserReview)
async def create_review(input: UserReviewCreate):
    review = UserReview(**input.model_dump())
    doc = review.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    await db.reviews.insert_one(doc)
    return review

@api_router.get("/reviews", response_model=List[UserReview])
async def get_reviews(
    casino_id: Optional[str] = None,
    status: Optional[str] = None,
    page: int = 1,
    limit: int = 10
):
    query = {}
    if casino_id:
        query['casino_id'] = casino_id
    if status:
        query['status'] = status
    
    skip = (page - 1) * limit
    reviews = await db.reviews.find(query, {"_id": 0}).sort("created_at", -1).skip(skip).limit(limit).to_list(limit)
    
    for review in reviews:
        if isinstance(review['created_at'], str):
            review['created_at'] = datetime.fromisoformat(review['created_at'])
    return reviews

@api_router.get("/reviews/count")
async def get_reviews_count(casino_id: Optional[str] = None, status: Optional[str] = None):
    query = {}
    if casino_id:
        query['casino_id'] = casino_id
    if status:
        query['status'] = status
    count = await db.reviews.count_documents(query)
    return {"count": count}

@api_router.get("/reviews/{review_id}", response_model=UserReview)
async def get_review(review_id: str):
    review = await db.reviews.find_one({"id": review_id}, {"_id": 0})
    if not review:
        raise HTTPException(status_code=404, detail="Review not found")
    if isinstance(review['created_at'], str):
        review['created_at'] = datetime.fromisoformat(review['created_at'])
    return review

@api_router.put("/reviews/{review_id}/status")
async def update_review_status(review_id: str, status: str):
    if status not in ["pending", "approved", "rejected"]:
        raise HTTPException(status_code=400, detail="Invalid status")
    
    result = await db.reviews.update_one(
        {"id": review_id},
        {"$set": {"status": status}}
    )
    if result.modified_count == 0:
        raise HTTPException(status_code=404, detail="Review not found")
    return {"message": "Review status updated successfully"}

@api_router.delete("/reviews/{review_id}")
async def delete_review(review_id: str):
    result = await db.reviews.delete_one({"id": review_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Review not found")
    return {"message": "Review deleted successfully"}

# ========= AFFILIATE LINKS =========

@api_router.post("/affiliate-links", response_model=AffiliateLink)
async def create_affiliate_link(input: AffiliateLinkCreate):
    link = AffiliateLink(**input.model_dump())
    doc = link.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    await db.affiliate_links.insert_one(doc)
    return link

@api_router.get("/affiliate-links", response_model=List[AffiliateLink])
async def get_affiliate_links(casino_id: Optional[str] = None):
    query = {}
    if casino_id:
        query['casino_id'] = casino_id
    
    links = await db.affiliate_links.find(query, {"_id": 0}).to_list(1000)
    for link in links:
        if isinstance(link['created_at'], str):
            link['created_at'] = datetime.fromisoformat(link['created_at'])
    return links

@api_router.get("/affiliate-links/{link_id}", response_model=AffiliateLink)
async def get_affiliate_link(link_id: str):
    link = await db.affiliate_links.find_one({"id": link_id}, {"_id": 0})
    if not link:
        raise HTTPException(status_code=404, detail="Affiliate link not found")
    if isinstance(link['created_at'], str):
        link['created_at'] = datetime.fromisoformat(link['created_at'])
    return link

@api_router.post("/affiliate-links/{link_id}/click")
async def track_affiliate_click(link_id: str):
    result = await db.affiliate_links.update_one(
        {"id": link_id},
        {"$inc": {"clicks": 1}}
    )
    if result.modified_count == 0:
        raise HTTPException(status_code=404, detail="Affiliate link not found")
    return {"message": "Click tracked successfully"}

@api_router.put("/affiliate-links/{link_id}", response_model=AffiliateLink)
async def update_affiliate_link(link_id: str, input: AffiliateLinkCreate):
    existing = await db.affiliate_links.find_one({"id": link_id})
    if not existing:
        raise HTTPException(status_code=404, detail="Affiliate link not found")
    
    update_data = input.model_dump()
    await db.affiliate_links.update_one({"id": link_id}, {"$set": update_data})
    
    updated = await db.affiliate_links.find_one({"id": link_id}, {"_id": 0})
    if isinstance(updated['created_at'], str):
        updated['created_at'] = datetime.fromisoformat(updated['created_at'])
    return AffiliateLink(**updated)

@api_router.delete("/affiliate-links/{link_id}")
async def delete_affiliate_link(link_id: str):
    result = await db.affiliate_links.delete_one({"id": link_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Affiliate link not found")
    return {"message": "Affiliate link deleted successfully"}

# ========= ADVERTISEMENTS =========

@api_router.post("/ads", response_model=Advertisement)
async def create_ad(input: AdvertisementCreate):
    ad = Advertisement(**input.model_dump())
    doc = ad.model_dump()
    doc['created_at'] = doc['created_at'].isoformat()
    if doc.get('start_date'):
        doc['start_date'] = doc['start_date'].isoformat()
    if doc.get('end_date'):
        doc['end_date'] = doc['end_date'].isoformat()
    await db.advertisements.insert_one(doc)
    return ad

@api_router.get("/ads", response_model=List[Advertisement])
async def get_ads(position: Optional[str] = None, active_only: bool = False):
    query = {}
    if position:
        query['position'] = position
    if active_only:
        query['is_active'] = True
        # Check date range if specified
        now = datetime.now(timezone.utc).isoformat()
        query['$or'] = [
            {"start_date": None, "end_date": None},
            {"start_date": {"$lte": now}, "end_date": None},
            {"start_date": None, "end_date": {"$gte": now}},
            {"start_date": {"$lte": now}, "end_date": {"$gte": now}}
        ]
    
    ads = await db.advertisements.find(query, {"_id": 0}).to_list(1000)
    for ad in ads:
        if isinstance(ad['created_at'], str):
            ad['created_at'] = datetime.fromisoformat(ad['created_at'])
        if ad.get('start_date') and isinstance(ad['start_date'], str):
            ad['start_date'] = datetime.fromisoformat(ad['start_date'])
        if ad.get('end_date') and isinstance(ad['end_date'], str):
            ad['end_date'] = datetime.fromisoformat(ad['end_date'])
    return ads

@api_router.post("/ads/{ad_id}/impression")
async def track_ad_impression(ad_id: str):
    result = await db.advertisements.update_one(
        {"id": ad_id},
        {"$inc": {"impressions": 1}}
    )
    return {"message": "Impression tracked"}

@api_router.post("/ads/{ad_id}/click")
async def track_ad_click(ad_id: str):
    result = await db.advertisements.update_one(
        {"id": ad_id},
        {"$inc": {"clicks": 1}}
    )
    return {"message": "Click tracked"}

@api_router.put("/ads/{ad_id}", response_model=Advertisement)
async def update_ad(ad_id: str, input: AdvertisementCreate):
    existing = await db.advertisements.find_one({"id": ad_id})
    if not existing:
        raise HTTPException(status_code=404, detail="Advertisement not found")
    
    update_data = input.model_dump()
    if update_data.get('start_date'):
        update_data['start_date'] = update_data['start_date'].isoformat()
    if update_data.get('end_date'):
        update_data['end_date'] = update_data['end_date'].isoformat()
    
    await db.advertisements.update_one({"id": ad_id}, {"$set": update_data})
    
    updated = await db.advertisements.find_one({"id": ad_id}, {"_id": 0})
    if isinstance(updated['created_at'], str):
        updated['created_at'] = datetime.fromisoformat(updated['created_at'])
    if updated.get('start_date') and isinstance(updated['start_date'], str):
        updated['start_date'] = datetime.fromisoformat(updated['start_date'])
    if updated.get('end_date') and isinstance(updated['end_date'], str):
        updated['end_date'] = datetime.fromisoformat(updated['end_date'])
    return Advertisement(**updated)

@api_router.delete("/ads/{ad_id}")
async def delete_ad(ad_id: str):
    result = await db.advertisements.delete_one({"id": ad_id})
    if result.deleted_count == 0:
        raise HTTPException(status_code=404, detail="Advertisement not found")
    return {"message": "Advertisement deleted successfully"}

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