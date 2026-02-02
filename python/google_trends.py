import sys
import json
import logging
from pytrends.request import TrendReq

# Setup logging
logging.basicConfig(level=logging.ERROR)
logger = logging.getLogger(__name__)

def fetch_google_trends(region='BR', hl='pt-BR'):
    try:
        pytrends = TrendReq(hl=hl, tz=360)
        
        # Get Realtime Trending Searches (usually better for "now", but limited by category)
        # OR Daily Trending Searches.
        
        # Method 1: trending_searches (Daily Trends)
        # result_df = pytrends.trending_searches(pn=region.lower()) 
        
        # Method 2: realtime_trending_searches (Last 24h, real-time)
        result_df = pytrends.realtime_trending_searches(pn=region)
        
        trends = []
        if not result_df.empty:
            # result_df columns: title, entityNames (list)
            for index, row in result_df.iterrows():
                trends.append({
                    "title": row['title'],
                    "entity_names": row['entityNames'] if 'entityNames' in row else [],
                    "rank": int(index) + 1
                })
                
                if len(trends) >= 10:
                    break
        
        print(json.dumps(trends))
        
    except Exception as e:
        logger.error(f"Error fetching Google Trends: {str(e)}")
        # Output empty JSON array on error to prevent PHP json_decode failure
        print("[]")
        sys.exit(1)

if __name__ == "__main__":
    region_arg = sys.argv[1] if len(sys.argv) > 1 else 'BR'
    fetch_google_trends(region=region_arg)
