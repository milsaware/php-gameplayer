# PHP GamePlayer

**A work in progress**

A server with access to one directory above the public folder is required to install and use this script. Upload the contents of httpd.www to your public folder, upload httpd.private to one directory above your public directory. Your public directory may be named something other than **httpd.www**. It may, for instance, be called **public** or your username. If this is the case you must open httpd.private/system/bootstrap.php and change the two instances of **httpd.www** on line 12 to the name of your public directory. Do the same for **httpd.private** on line 15 to whatever your private directory is called.

The app runs on SQLite so there's no need for any database installation.

There currently isn't a CMS associated with the app so all modifications, including the adding of new games, need to be done manually.

**Features**

- Responsive design
- Over 600 games built in, ready to play
- Sign up, log in and log out functionality
- Comments section for each game with response functionality
- Like/Dislike/Favourites for games and comments
- Game thumb scrollbars
- User profile pages with access to settings, following and games liked
- Ability to follow accounts

**Known bugs**

- Profile pages aren't responsive
- Password key for change password needs to be readily accessible to the user
- Needs a CMS admin panel

**Languages used**

PHP, HTML, Javascript, CSS

**Framework**
ozboware PHP MVCF 1.4.4

**Screenshots**
![homepage](https://user-images.githubusercontent.com/95859352/151651122-c56f2d53-873e-419c-977c-45d7ed7cf8b5.png)
![gamscreen](https://user-images.githubusercontent.com/95859352/151651129-e4273e8e-a827-462b-b29d-41b22de375f3.png)
![signinscreen](https://user-images.githubusercontent.com/95859352/151651133-e5a6af4f-4142-4b92-a367-6b1d82a07357.png)
![profile](https://user-images.githubusercontent.com/95859352/151651140-e8647f4b-cbcc-496c-8379-d8b12dcedf81.png)
![logout](https://user-images.githubusercontent.com/95859352/151651142-b6e26334-2f05-471a-9ad6-e793d59a96de.png)
