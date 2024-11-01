=== WP Image Sizes ===
Contributors: aiwatech
Tags: images, sizes, thumbnails, gallery, image sizes, media uploader, media library, featured image, multiple image creation, selected image sizes, bulk image sizes, disable image sizes, prevent unnecessary images
Requires at least: 4.7
Requires PHP: 5.2.4
Stable tag: 1.1.3
Tested up to: 5.7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Select the only image sizes for post types you want to be generated. Eliminate unnecessary image sizes.

== Description ==

In modern web designs, designers create different sizes of images on the web pages for attractive layout. If a web page is built using wordpress, a developer has to register every image size in theme to make sure, if client uploads an image of different size, it does not break the layout.

With a new WordPress installation, by default it creates three sizes e.g. thumbnail (150 x 150), medium (300×300), large (1024×1024) which means whenever a new image will be uploaded, WordPress will keep at least four copies of images on the server, one original image and 3 cropped image copies based on image sizes.

When developer/designer needs different size of image, they will register more image sizes e.g. hero, product thumbnail, product large image, team member image etc depending on design of the page.

Now lets say we have 3 default and 3 custom image sizes defined in theme, and now whenever a new image will be uploaded, WordPress will create 6 image copies and 1 original image even we need to use only 1 for a specific area on the website, remaining copies will remain on the server and will occupy space. Imagine when we have lots of posts, pages having different images, how many unnecessary images WordPress creates on the server which are useless and just eat server space. In case of Woo-Commerce website if you need to upload a new slider image for home page or a hero image for about page, it will create a lots of copies including different sizes of products image sizes which woo-commerce registers itself.

Ultimately website speed gets effected and server space as well.

To solve this issue, we built a plugin WP Image Sizes this plugin allows user to decide which image sizes should be created for every image being uploaded through WordPress.

After installing WP Image Sizes, it will create a setting page from where user can assign image sizes to each CPT. e.g. user can assign one size for hero/slider CPT, one size for team member, testimonials. After assigning image sizes to each CPT when user will upload a new image, WP Image Sizes will only allow WordPress to create copies of image sizes which were assigned to the CPT.

In case user tries to upload an image from a Media, it will show options in media uploader to select which image size user wants and WP Image Sizes will only create that image size.

WP Image Sizes plugin saves a lot of space on the server and plays a role to make WordPress website a bit fast.


== Installation ==

From your WordPress dashboard

1. **Visit** Plugins > Add New
2. **Search** for "WP Image Sizes"
3. **Install** WP Image Sizes from search results
3. **Activate** WP Image Sizes after plugin has been installed
4. **Click** on the settings menu item "WP Image Sizes" and assign image sizes to every post types

== Frequently Asked Questions ==

= Does it have a pro version? =
Yes, a pro version is available with more features [See here](https://aiwatech.com/wp-image-sizes).

= Does it cache images it creates? =
No, It doesn't create any cache images and the images created will not be broken even if the plugin is deactivate/removed.

= Why use it? =
WPIS becomes more useful when used with e-commerce plugins like woocommerce, woocommerce requires to create many image sizes for prdoucts. Whenever a user uploads a non-product image, wordpress creates all the images for woo-coomerce as well.
i.e User uploads an image for slider/banner, Wordpress will also create image thumbnails for the products. WPIS helps to select only slider/banner size and avoid to generation of woocommerce versions of the image.


== PRO ==
WP Image Sizes plugin is also available in a professional version which includes more features [See here](https://aiwatech.com/product/wp-image-sizes-pro/)


== Screenshots ==
1. Assignment of image sizes to post types
2. The image sizes will show in media uploader
3. ProFeature - Create batch/bulk image sizes for selected post types
4. ProFeature - Delete batch/bulk image sizes for selected post types

== Changelog ==
= 1.1.3 = 
* Compatibility Check with Wordpress 5.7.2
= 1.1.2 = 
* Compatibility fix with pro version when generating runtime images
= 1.1.1 = 
* Code changes for compatibility with pro version

= 1.1.0 = 
* Media uploader image size checkboxes position changed
* Media uploader image size checkboxes loading approach changed

= 1.0.8 = 
* Option to disable all image sizes added to wpis settings

= 1.0.7 = 
* Bug fixes

= 1.0.6 = 
* Disable sizes fixes

= 1.0.4 = 
* Media upload image sizes bug fix
* Option to disable all image sizes added

= 1.0.3 = 
* Session storage changed to fix register_globals conflict

= 1.0.2 = 
* Plugin core changes
* Wordpress 5.5.3 compatibility

= 1.0.1 = 
* Session unset on settings save
* Redirect implemented on plugin activate

= 1.0.0 = 
* Initial release of WP Image Sizes