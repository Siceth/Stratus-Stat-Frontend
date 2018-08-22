# Stats Services

I write a lot of small independent scripts based on crawling or calculating foreign data.  Most of them are private, but what's listed here is public if you find it helpful.  It includes:

* **stratus** A mirror for caching pages on Stratus Network ([https://stratus.network/](https://stratus.network/))
* **stratus-maps** An image cache repository for Stratus maps that searches natively and on [MCResourcePile](https://github.com/MCResourcePile/mcresourcepile.github.io)
* **stratus-topdonor** Simply get the in-game username of the person listed as the top donor [here](https://stratusnetwork.buycraft.net/).

These all assume an Apache/PHP web server.  Remember to `chmod -R u+rwX,go+rX,go-w /var/www/html` and `chown` accordingly!