<p align="center">
  <img src="https://cdn.rawgit.com/lutsen/niftyfolder/master/niftyfolder-logo.svg" width="100" alt="NiftyFolder">
</p>
<p align="center"><b>NiftyFolder turns any folder containing Google docs on your Google Drive into a website in minutes</b></p>
<p align="center">(and working on turning those minutes in to seconds 😀)</p>


NiftyFolder in a nutshell
--------------------------
Create a folder on your Google Drive. Add Google docs to this folder for all the pages of your website. Add sub-folders for different sections of your website if you want. Install NiftyFolder on your webserver. Connect to your Google Drive. Select your website folder on the NiftyFolder admin page. Done!

NiftyFolder is a project written in PHP. It uses the Google API client PHP SDK.


Requirements
------------

- PHP 5.6.3 or newer
- [A Google API project](https://console.developers.google.com/apis/library)


Install NiftyFolder
--------------------

Install NiftyFolder and its dependencies with [Composer](https://getcomposer.org/) with this command: `$ php composer.phar create-project lutsen/serge [project-name] 0.5`  
(Replace [project-name] with the desired directory name for your new project)  

The Composer script creates the *cache* directory, and *config.php* file for you.  

Update *config.php* with:
- your server paths
- the admin user(s) and their password(s)

NiftyFolder uses [Slim HTTP Basic Authentication middleware](http://www.appelsiini.net/projects/slim-basic-auth) to authenticate users for the admin interface. Make sure to change the password in *config.php*, and use HTTPS to login securely.

Create a Google API project web application.  

Download the json client secret file from you Google API project, rename it to *client_secret.json* and add it to the NiftyFolder root directory.  


#### How to create a Google API project ####

1. Go to the [Google API Console](https://console.developers.google.com/project/_/apiui/apis/library). Create a new one by selecting Create a new project.
2. Select the Google Drive API for your project.
4. Choose to create a webapp.
4. In the Credentials tab, select the New credentials drop-down list, and choose OAuth client ID.
5. In the Authorized Redirect URIs field, enter this URL: [your app url]/admin/oauth2callback
6. Press the Create button.
7. Download the client_secret json file (the download button is on the right).
8. Rename this file to *client_secret.json* and add it to the NiftyFolder root directory.


Using NiftyFolder
-----------------

#### Preparing your website on your Google Drive ####

The content of a NiftyFolder website lives in a folder on your Google Drive. All Google Docs in this folder are shown as pages on your website. You can set a homepage by putting a Google Doc in the root of the website folder on your Google Drive and make sure it is the first document in the alphabettical order of your file listing. You can force the order by putting \[1\] (a 1 between square brackets) at the start of your document name. This [1] is not displayed on your website.

You can add sub-folders inside this folders to create categories in the menu of your website.  
Any files other then Google Docs are presented as downloads in your webiste menu.


#### Connect to a Google account ####

The first time you access NiftyFolder, you are redirected to the admin page. To access the admin page, you have to log in with the username and password you added to the *config.php* file.  
On the admin page you are asked to connect to the Google account of the Google Drive you want to use. You need to authorise NiftyFolder for your Google account to make it work. After authorising the NiftyFolder web application, the OAuth credentials are saved in the *credentials.json* file.  
*Most likely, your app is not verified by Google. Because of this, you will get an [unverified app warning](https://support.google.com/cloud/answer/7454865). Because you will  be the only person using the app to connect your website to your Google Drive, you can ignore this warning. Click on the "Advanced" options and connect to the app.*


#### Select a website folder ####

After you connected to your Google account you are redirected to the admin page. Here you have to select the folder the documents of your website are in. **If you have a lot of folders and documents on your Google Drive, the loading of all the folders of your website might take some time. Don't worry, this is normal**. After they have loaded choose the right folder and click *Set folder*.


### Generating the tree.json file ###

After setting your website folder you are redirected back to the admin page again. Now the tree.json file for your website needs to be created. This file contains the structure of your website. To generate it, just visit the homepage of your website by clicking on the *Load your website* link. **Heads up**: every time you change the *structure* of your website, you need to delete and regenerate the tree.json file!


### Editing your website ###

You can edit the pages of your website on Google Drive, just like you would edit any other Google Docs file.


### Adding, deleting or moving files to different folders ###

To add pages to your website you can add new Google Docs to your website folder. You can also delete pages you no longer want. If you want to keep them but not display them on your website you can move them out of your website folder. You can also move files around or add new sub-folders to your website folder.  
**Keep in mind that after adding, deleting or moving files or folders, you have to delete the existing tree.json file.**  
You can do this on the admin page of your website. You can visit this page via [your website url]/admin  

**Heads up**: Because of the way Google Drive works, it is not enough to move a page to the Google Drive trash to remove it from your website. You have to empty the trash to remove it. You can also move it to a folder outside your website folder.


### Changing the order of the pages on your website ###

By default, pages in the website navigation are displayed alphabetticaly. You can change this by adding a numer surrounded by square brackets in front of the page title (for example: \[2\]). This number won't be displayed, but NiftyFolder will keep the order of these numbers represent.


### Adding a youtube movie to a page on your website ###

You can't add Youtube movies to display in Google Docs, but you can in NiftyFolder! To display a Youtube video on your website, add a link to it in your Google Doc, surrounded by square brackets, like this: *\[https://youtu.be/tA3MIL2taW8\]*. The video will display in full page-width on your website.



NiftyFolder project structure
------------------------------

An overview of the directories of a NiftyFolder and their contents.


#### cache (directory) ####

The Composer script creates this directory in the project root to hold the Twig template engine cache files. If updates in your templates are not showing; remember to clear the cache directory.


#### controllers (directory) ####

Contains these controllers:

- content.php  
Contains methods to read and manipulate content from the connected Google Drive folder.
- drive.php  
Helps you connect to your Google drive. Contains the client and service properties which are Google_Client and oogle_Service_Drive objects.
- tree.php  
Contains methods to read and manipulate folder trees.


#### public (directory) ####

Contains the *index.php* and *.htaccess* file. The *index.php* file contains the autoloader, includes the route files, and includes some other files and settings.  

The public directory also contains the css, less and img diretories.

*The "public" directory is the directory holding your public web pages on your webserver. It's name can vary on different hosting providers and -environments. Other common names are "html", "private-html", "www" or "web". Put the files of the "public" directory in this public directory on your webserver.*


#### routes (directory) ####

Contains the different route files. Each route file is automatically loaded, and contains the routes for your project. Routes are built with [Slim](http://www.slimframework.com/).


#### templates (directory) ####

This directory contains the template files. The subdirectory *admin* contains the template files for the admin page. The subdirectory *public* contains the template files for the public page.  
NiftyFolder uses HTML templates and the [Twig template engine](https://twig.symfony.com/) to display the website layout. Feel free to create your own templates!


#### vendor (directory) ####

Created by [Composer](https://getcomposer.org/) when installing the project dependencies.


#### config.php (file) ####

The Composer script renames the *config_example.php* file to *config.php*. The *config.php* file is needed for a NiftyFolder project to work. Remember to add the necessary details.


[NiftyFolder](https://www.niftyfolder.com) is a project of [Lútsen Stellingwerff](http://lutsen.net/).

Google Drive is a trademark of Google Inc. Use of this trademark is subject to Google Permissions. NiftyFolder is not part of or associated with Google Inc.