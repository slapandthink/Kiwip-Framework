# [Kiwip Framework](http://slapandthink/kiwip-framework)
Kiwip Framework is a Wordpress Theme Development Framework for developer.
It makes things easier to create custom post type, template options page, widget, shortcode and many other things.

This personal project is the product of many houres of development and knowledge.

The developpment is still in progress.

## Features
Kiwip Framework provides many things, and has been developed to be extensible:
### General
- PHP5
- OOP
- Use standards Wordpress function
- ...

### Custom Post Type
- Add and manage easily
- Support multilang
- Icons support
- Many extra metaboxes field type (and easily add yours)
- Support of the Wordpress AJAX uploader (with the library)
- ...

### Theme Options
- Add and manage options page easily
- Icons support
- Many extra metaboxes field type (and easily add yours)
- Support of the Wordpress AJAX uploader (with the library)
- Input field verification
- ...

### Widgets
- Add and manage easily
- ...

### Shortcodes
- Add and manage your custom shortcode easily
- Accordion
- Columns
- Cryptemail
- Logintoview

### Internal Framework Options
- Delete all Wordpress revision
- ...

--

## Documentation
I hope on day, I will publish a simple documentation for using this Wordpress Framework, but now i haven't the time to do this. When the documentation will be publish, it will storred on a [dedicate page](http://slapandthink/kiwip-framework). However, the code is very basic and require no elevate knowledge for understanting it. Furthermore, all the code is well commented, you shouldn't have any problem to understand it.

### Installation
To install the Kiwip Framework, you have just to upload the Kiwip Framework folder into your Wordpress Theme Folder (wp-content/themes/YOURT_HEME/).
To configure ans set the Framework, just copy the 'Kiwip-Framework-options.php' into your Wordpress Theme Folder (wp-content/themes/YOURT_HEME/) and include it in your function.php.

```php
/**
 * @file functions.php
 *
 * ==============================================================
 * ======================== SIMPLE USAGE ========================
 * ==============================================================
 * How to use the Kiwip Framework
 * Simply include the Kiwip-Framework-options.php file at the top of your themes functions.php file, like so:
 * get_template_part('Kiwip-Framework-options');
 * Then change the settings as written in the Kiwip-Framework-options.php file.
 */
get_template_part('Kiwip-Framework-options');
```

## Compatibility
Works with Wordpress 3.x.

## Version
The framework is still in development, but ready to use.

## Contributing
Like this project was an individual iniciative, i did it alone, but all contributions are accepted (require validation).
To contribute, just contact me by github or on my website.

Benjamin Cabanes

--

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.