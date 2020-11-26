## Creating a Cloudflare API Token for this System

1. Go [here](https://dash.cloudflare.com/profile/api-tokens) to start the process. Click "Create Token".
2. Use the Template "Edit zone DNS". If the template doesn't exist, skip to the "Custom Token" section below.
3. Under Zone Resources, Include the Zone you want to modify using this program.
4. You may choose to use IP Address Filtering and/or a TTL for this Token to enhance security. This guide will not cover these.
5. Click "Continue to summary", Then "Create Token"
6. Note down the API Token given. **You will NOT see this token again.**
7. Continue off Step 4 at the [README](README.md)

## Custom Token
So, you either don't trust Cloudflare to provide an accurate template (at which, Why are you using Cloudflare?), 
or want to know what permissions this program is getting, or the template doesn't exist anymore.

1. Under Permissions, Grant the "Zone - DNS - Edit" Permission. We need this to lookup and edit A and AAAA entries to do the DDNS Update.
2. Under Zone Resources, "Include - Specific Zone - \<The Domain You Wish to Allow this Program to Manage\>" at the very minimum is needed. You may allow more, but they will not be used by this instance.
3. You may choose to use IP Address Filtering and/or a TTL for this Token to enhance security. This guide will not cover these.
4. Click "Continue to summary", Then "Create Token"
5. Note down the API Token given. **You will NOT see this token again.**
6. Continue off Step 4 at the [README](README.md)
