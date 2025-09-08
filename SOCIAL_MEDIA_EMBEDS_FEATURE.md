# Social Media Embeds Feature

## Overview
This feature allows users to embed social media content from various platforms directly into articles. The embeds are displayed above the tags section and ads, providing a seamless integration of social media content with article content.

## Supported Platforms
- **Twitter/X**: Embed tweets, threads, and other Twitter content
- **Facebook**: Embed Facebook posts, videos, and other content
- **Instagram**: Embed Instagram posts, reels, and stories
- **TikTok**: Embed TikTok videos
- **Custom**: Any other platform that provides embed codes

## How to Use

### 1. Creating/Editing Articles
When creating or editing an article in the admin panel, you'll find a new "Social Media Embeds" section after the YouTube video field.

### 2. Getting Embed Codes

#### Twitter/X
1. Go to the tweet you want to embed
2. Click the "..." menu on the tweet
3. Select "Embed Tweet"
4. Copy the HTML code provided
5. Paste it into the "Twitter/X Embed Code" field

#### Facebook
1. Go to the Facebook post you want to embed
2. Click the "..." menu on the post
3. Select "Embed"
4. Copy the HTML code provided
5. Paste it into the "Facebook Embed Code" field

#### Instagram
1. Go to the Instagram post you want to embed
2. Click the "..." menu on the post
3. Select "Embed"
4. Copy the HTML code provided
5. Paste it into the "Instagram Embed Code" field

#### TikTok
1. Go to the TikTok video you want to embed
2. Click the "Share" button
3. Select "Embed"
4. Copy the HTML code provided
5. Paste it into the "TikTok Embed Code" field

#### Custom Platforms
For any other platform that provides embed codes:
1. Get the embed code from the platform
2. Paste it into the "Custom Embed Code" field

### 3. Display
You can embed social media content **directly within your article content**:

**How to Use:**
1. **Add Embed Codes**: First, paste your embed codes in the "Social Media Embeds" section of the form
2. **Insert in Content**: In the TinyMCE editor, click where you want the embed to appear
3. **Use Embed Buttons**: Click the appropriate embed button (Twitter, Facebook, Instagram, TikTok, Custom) in the toolbar
4. **Result**: A placeholder `[TWITTER_EMBED]` (or similar) will be inserted in your content
5. **Save Article**: When you save, the placeholder will be replaced with the actual embed

**Example Workflow:**
1. Paste Twitter embed code in "Twitter/X Embed Code" field
2. In article content, write: "Here's what happened on Twitter:"
3. Click the "Twitter" button in TinyMCE toolbar
4. Continue writing: "This shows the community reaction."
5. Save the article - the Twitter embed will appear between those two sentences

**Benefits:**
- ✅ **Precise Control**: Place embeds exactly where you want them
- ✅ **Better Flow**: Integrate social media content naturally into your narrative
- ✅ **Flexible Layout**: Mix embeds with text, images, and other content
- ✅ **Professional Look**: Embeds appear seamlessly within the article flow

**Display Order:**
1. Article content (with inline embeds if any)
2. Ad banner section
3. Tags section
4. Social sharing section

**Note:** The separate "Social Media Content" section has been removed since inline embeds provide better control and user experience.

## Technical Details

### Database Fields Added
- `twitter_embed` (TEXT, nullable)
- `facebook_embed` (TEXT, nullable)
- `instagram_embed` (TEXT, nullable)
- `tiktok_embed` (TEXT, nullable)
- `custom_embed` (TEXT, nullable)

### Styling
Each embed type has its own CSS styling:
- **Twitter**: Light gray background (#f8f9fa)
- **Facebook**: Facebook blue background (#f0f2f5)
- **Instagram**: Instagram white background (#fafafa)
- **TikTok**: Light gray background (#f8f9fa)
- **Custom**: Light gray background (#f8f9fa)

### Responsive Design
- Embeds are fully responsive
- On mobile devices, padding is reduced for better viewing
- All embeds maintain their aspect ratios

### Security
- Embed codes are stored as-is and rendered using `{!! !!}` syntax
- This allows full HTML/JavaScript from social media platforms
- Only trusted embed codes should be used

## Best Practices

1. **Test Embeds**: Always preview articles with embeds before publishing
2. **Mobile Check**: Verify embeds look good on mobile devices
3. **Performance**: Don't add too many embeds as they can slow down page loading
4. **Content Relevance**: Only embed social media content that's relevant to the article
5. **Backup Content**: Consider that embedded content might become unavailable

## Troubleshooting

### Embed Not Displaying
1. Check if the embed code is complete and valid
2. Verify the original post is still available
3. Check browser console for JavaScript errors
4. Ensure the platform allows embedding

### Styling Issues
1. Check if the embed code includes conflicting CSS
2. Verify responsive design on different screen sizes
3. Test in different browsers

### Performance Issues
1. Limit the number of embeds per article
2. Consider lazy loading for embeds
3. Monitor page load times

## Future Enhancements
- Automatic embed validation
- Preview functionality for embeds
- Embed analytics tracking
- Support for more platforms
- Embed caching for better performance
