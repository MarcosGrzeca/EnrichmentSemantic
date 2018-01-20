
cd /home/ec2-user/GetOldTweets-python/

python Exporter.py --querysearch "Mardigras"


TwitterCriteria: A collection of search parameters to be used together with TweetManager.
setUsername (str): An optional specific username from a twitter account. Without "@".
setSince (str. "yyyy-mm-dd"): A lower bound date to restrict search.
setUntil (str. "yyyy-mm-dd"): An upper bound date to restrist search.
setQuerySearch (str): A query text to be matched.
setTopTweets (bool): If True only the Top Tweets will be retrieved.
setNear(str): A reference location area from where tweets were generated.
setWithin (str): A distance radius from "near" location (e.g. 15mi).
setMaxTweets (int): The maximum number of tweets to be retrieved. If this number is unsetted or lower than 1 all possible tweets will be retrieved.