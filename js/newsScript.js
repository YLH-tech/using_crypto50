const apiUrls = [
  'https://api.rss2json.com/v1/api.json?rss_url=https://cointelegraph.com/rss',
  'https://api.rss2json.com/v1/api.json?rss_url=https://cryptonews.com/news/rss/'
];

// Function to fetch and display crypto news
async function fetchCryptoNews() {
  try {
    const newsList = document.getElementById('news-list');
    newsList.innerHTML = ''; // Clear old news

    // Get the current date and date two days ago
    const now = new Date();
    const twoDaysAgo = new Date(now);
    twoDaysAgo.setDate(now.getDate() - 2); // Subtract two days

    // Fetch from multiple sources
    for (const apiUrl of apiUrls) {
      try {
        const response = await fetch(apiUrl);
        const data = await response.json();

        // Check if there are items to display
        if (data.items && data.items.length > 0) {
          // Display news items from the last two days only
          data.items.forEach((item) => {
            const publishedDate = new Date(item.pubDate);

            // Check if the news item is within the last two days
            if (publishedDate >= twoDaysAgo) {
              const newsItem = document.createElement('li');
              newsItem.innerHTML = `
                <strong>${item.title}</strong><br>
                <em>${publishedDate.toLocaleString()}</em><br>
                <a href="${item.link}" target="_blank">Read more</a>
              `;
              newsList.appendChild(newsItem);
            }
          });
        } else {
          // Display a message if no news is available
          const noNewsMessage = document.createElement('li');
          noNewsMessage.innerText = 'No recent news available.';
          newsList.appendChild(noNewsMessage);
        }
      } catch (error) {
        // Skip problematic feed and continue with the next
      }
    }
  } catch (error) {
    // Handle general errors without logging them
  }
}

// Fetch news when the page loads
fetchCryptoNews();

// Optional: refresh news every 5 minutes
setInterval(fetchCryptoNews, 300000); // 300,000 ms = 5 minutes
