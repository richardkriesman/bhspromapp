# BHSPromApp

BHSPromApp is a web-based registration, finance, and seating assignment system for events, created for L.D. Bell High School to use for their senior prom. I created this as a volunteer project during my Junior and Senior years of high school there. Yes, it's PHP. Yes, it's janky. Yes, the code is spaghetti. But I'm still putting it up here for reasons. Maybe I'll actually pick it up and mess with it some time.

BHSPromApp can help you:
 - Register attendees and print tickets
 - Provide seating assignments
 - Track and report event revenue
 
 BHSPromApp supports multiple events and live collaboration, as well as Active Directory integration (for very specific setups). It supports Chrome, Firefox, and IE11 and up. Due to circumstances that prevented the use of a dedicated database server, SQLite is used for the database. It should work fine for a few people, but will definitely be a bottleneck in a large scale deployment.

### Installation

Installation is pretty simple process. Throw it on a PHP server, give write access to the "database" folder, fill out the configuration file in "include/config.inc.php", and away you go. Although frankly, this may not be much use to you. As I said previously, it was made specifically for the high school I went to, and was designed to meet its specific requirements.

### License

If you want to try it out or work on it, feel free to. It's licensed under the GPLv3, so you can use it for whatever purpose you want as long as it's not incorporated into any other commercial software.

**Notice:** BHSPromApp uses BarcodePHP (now Barcode Bakery) for generating barcodes, and a license is required for commerical use.
