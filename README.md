# cfupdate
Cloudflare DDNS Updater (via HTTP Requests)

A fork of ScottHelme/CloudFlareDDNS, This project implements additional features that I wanted for what I use Cloudflare for.

## Usage

1. Setup a PHP Compatible Server
2. Get your Cloudflare Global API Key from [here](https://dash.cloudflare.com/profile/api-tokens)
3. Register your domain with Cloudflare.
4. Fill out the config.php file with your information.
(For each updatable subdomain you want.)
5. Go to [here](https://www.random.org/strings/?num=1&len=20&digits=on&upperalpha=on&loweralpha=on&unique=on&format=plain&rnd=new) to get a string for a "Key"
6. In the config, enter the key generated in "KEY_GOES_HERE", and the subdomain (without your site.tld) to the right of it.
7. The last updateable domain in the list should not have a comma at the end.


## Do I use this?
Yes! https://www.rslowe.net is on Cloudflare, and while that site doesn't update its IP regularly, certain sites on rslowe.net do so via this system.
