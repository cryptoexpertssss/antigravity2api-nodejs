import asyncio
from motor.motor_asyncio import AsyncIOMotorClient
import os
from datetime import datetime, timezone
import uuid

# MongoDB connection
mongo_url = os.environ.get('MONGO_URL', 'mongodb://localhost:27017')
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ.get('DB_NAME', 'test_database')]

async def seed_data():
    print("Starting data seeding...")
    
    # Clear existing data
    await db.categories.delete_many({})
    await db.articles.delete_many({})
    await db.casinos.delete_many({})
    
    # Seed Categories
    categories = [
        {
            "id": str(uuid.uuid4()),
            "name": "Casino",
            "slug": "casino",
            "description": "Latest casino news and reviews",
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "Sports Betting",
            "slug": "sports-betting",
            "description": "Sports betting news and updates",
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "Industry News",
            "slug": "industry-news",
            "description": "Gaming industry news and analysis",
            "created_at": datetime.now(timezone.utc).isoformat()
        }
    ]
    
    await db.categories.insert_many(categories)
    print(f"✓ Seeded {len(categories)} categories")
    
    # Seed Articles
    articles = [
        {
            "id": str(uuid.uuid4()),
            "title": "Top Online Casinos Launch Exclusive Holiday Bonuses",
            "slug": "top-online-casinos-holiday-bonuses",
            "content": "<p>The holiday season brings exciting opportunities for online casino players as leading platforms announce exclusive bonuses and promotions. These special offers include enhanced welcome packages, deposit bonuses, and free spin opportunities.</p><p>Players can take advantage of increased bonus percentages and extended validity periods during this festive season. Major operators are competing to attract new players with their most generous offers of the year.</p>",
            "excerpt": "Leading online casinos announce special holiday promotions with enhanced bonuses and exclusive offers for players.",
            "category_id": categories[0]["id"],
            "author": "John Smith",
            "featured_image": None,
            "status": "published",
            "meta_description": "Discover the best holiday casino bonuses and exclusive promotions from top online gambling sites",
            "tags": ["casino", "bonuses", "promotions"],
            "created_at": datetime.now(timezone.utc).isoformat(),
            "updated_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "title": "New Jersey Online Casinos Break Revenue Records in December",
            "slug": "nj-online-casinos-revenue-record",
            "content": "<p>New Jersey's online casino industry has achieved unprecedented success, breaking revenue records in December. The state's iGaming market continues to demonstrate strong growth, driven by increased player engagement and new market entrants.</p><p>Industry analysts attribute this success to enhanced user experiences, mobile optimization, and competitive promotional strategies. The Garden State maintains its position as a leader in regulated online gambling.</p>",
            "excerpt": "New Jersey's online gambling market reaches new heights with record-breaking revenue figures in December.",
            "category_id": categories[0]["id"],
            "author": "Sarah Johnson",
            "featured_image": None,
            "status": "published",
            "meta_description": "New Jersey online casinos achieve record revenue in December 2025",
            "tags": ["new-jersey", "revenue", "online-casinos"],
            "created_at": datetime.now(timezone.utc).isoformat(),
            "updated_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "title": "Super Bowl Betting: Expert Predictions and Best Sportsbook Offers",
            "slug": "super-bowl-betting-predictions",
            "content": "<p>As the Super Bowl approaches, sports betting enthusiasts are analyzing odds and seeking the best promotional offers from leading sportsbooks. This year's championship promises exciting betting opportunities across multiple markets.</p><p>Expert handicappers are sharing their insights on point spreads, over/under totals, and prop bets. Major sportsbooks are offering enhanced odds and risk-free bet promotions for the big game.</p>",
            "excerpt": "Get expert Super Bowl betting predictions and discover the best sportsbook promotions for the championship game.",
            "category_id": categories[1]["id"],
            "author": "Mike Davis",
            "featured_image": None,
            "status": "published",
            "meta_description": "Expert Super Bowl betting predictions and top sportsbook offers for 2025",
            "tags": ["sports-betting", "super-bowl", "predictions"],
            "created_at": datetime.now(timezone.utc).isoformat(),
            "updated_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "title": "Mobile Gaming Revenue Surpasses Desktop for First Time",
            "slug": "mobile-gaming-surpasses-desktop",
            "content": "<p>A significant shift in the online gambling landscape has occurred as mobile gaming revenue officially surpasses desktop for the first time in industry history. This milestone reflects changing player preferences and improved mobile technology.</p><p>Mobile-optimized platforms and dedicated apps have enhanced user experiences, making gambling on smartphones and tablets more convenient than ever. Industry leaders are investing heavily in mobile-first strategies.</p>",
            "excerpt": "Mobile gaming achieves historic milestone as revenue exceeds desktop gambling for the first time.",
            "category_id": categories[2]["id"],
            "author": "Emily Chen",
            "featured_image": None,
            "status": "published",
            "meta_description": "Mobile gambling revenue surpasses desktop in historic industry shift",
            "tags": ["mobile-gaming", "industry-trends", "revenue"],
            "created_at": datetime.now(timezone.utc).isoformat(),
            "updated_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "title": "New Responsible Gaming Tools Launched by Major Operators",
            "slug": "responsible-gaming-tools-launch",
            "content": "<p>Leading online gambling operators have announced the launch of enhanced responsible gaming tools designed to promote safer gambling practices. These new features include deposit limits, session timers, and self-exclusion options.</p><p>The industry continues to prioritize player protection and responsible gambling initiatives. These tools provide players with greater control over their gaming activities and support healthy gambling habits.</p>",
            "excerpt": "Major gambling operators introduce enhanced responsible gaming tools to promote safer play.",
            "category_id": categories[2]["id"],
            "author": "David Williams",
            "featured_image": None,
            "status": "published",
            "meta_description": "New responsible gaming tools from major operators promote safer gambling",
            "tags": ["responsible-gaming", "player-protection", "tools"],
            "created_at": datetime.now(timezone.utc).isoformat(),
            "updated_at": datetime.now(timezone.utc).isoformat()
        }
    ]
    
    await db.articles.insert_many(articles)
    print(f"✓ Seeded {len(articles)} articles")
    
    # Seed Casino Listings
    casinos = [
        {
            "id": str(uuid.uuid4()),
            "name": "BetMGM Casino",
            "rank": 1,
            "logo_url": "https://placehold.co/200x100/667eea/white?text=BetMGM",
            "offer_title": "Up to $1,000 Deposit Match",
            "offer_details": "Plus $25 on the House",
            "features": [
                "100% Deposit Match up to $1,000",
                "1000+ Casino Games Available",
                "24/7 Customer Support"
            ],
            "promo_code": "PLAYBONUS",
            "review_link": "/reviews/betmgm",
            "claim_link": "https://example.com/betmgm",
            "rating": 5.0,
            "is_featured": True,
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "DraftKings Casino",
            "rank": 2,
            "logo_url": "https://placehold.co/200x100/10b981/white?text=DraftKings",
            "offer_title": "$2,000 Deposit Bonus",
            "offer_details": "Plus 50 Bonus Spins",
            "features": [
                "New Player Bonus up to $2,000",
                "500+ Premium Slots",
                "Fast Payouts in 24-48 Hours"
            ],
            "promo_code": "DKBONUS",
            "review_link": "/reviews/draftkings",
            "claim_link": "https://example.com/draftkings",
            "rating": 4.9,
            "is_featured": True,
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "Caesars Casino",
            "rank": 3,
            "logo_url": "https://placehold.co/200x100/f59e0b/white?text=Caesars",
            "offer_title": "100% Match up to $1,250",
            "offer_details": "Plus 2,500 Reward Credits",
            "features": [
                "First Deposit Match Bonus",
                "Live Dealer Games Available",
                "Caesars Rewards Program"
            ],
            "promo_code": "CAESARS100",
            "review_link": "/reviews/caesars",
            "claim_link": "https://example.com/caesars",
            "rating": 4.8,
            "is_featured": False,
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "FanDuel Casino",
            "rank": 4,
            "logo_url": "https://placehold.co/200x100/3b82f6/white?text=FanDuel",
            "offer_title": "$1,000 Play it Again",
            "offer_details": "Get up to $1,000 Back",
            "features": [
                "Up to $1,000 Refund on Losses",
                "Exclusive FanDuel Slots",
                "Same Day Withdrawals"
            ],
            "promo_code": None,
            "review_link": "/reviews/fanduel",
            "claim_link": "https://example.com/fanduel",
            "rating": 4.7,
            "is_featured": False,
            "created_at": datetime.now(timezone.utc).isoformat()
        },
        {
            "id": str(uuid.uuid4()),
            "name": "BetRivers Casino",
            "rank": 5,
            "logo_url": "https://placehold.co/200x100/8b5cf6/white?text=BetRivers",
            "offer_title": "100% Deposit Match",
            "offer_details": "Up to $500",
            "features": [
                "Generous Welcome Bonus",
                "iRush Rewards Program",
                "400+ Casino Games"
            ],
            "promo_code": "RIVERS500",
            "review_link": "/reviews/betrivers",
            "claim_link": "https://example.com/betrivers",
            "rating": 4.6,
            "is_featured": False,
            "created_at": datetime.now(timezone.utc).isoformat()
        }
    ]
    
    await db.casinos.insert_many(casinos)
    print(f"✓ Seeded {len(casinos)} casino listings")
    
    print("✅ Data seeding completed successfully!")

if __name__ == "__main__":
    asyncio.run(seed_data())
