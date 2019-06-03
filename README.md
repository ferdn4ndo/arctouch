# ArcTouch FullStack Code Challenge v2

by Fernando Augusto Constantino da Silva

## 1. WHAT WAS REQUESTED

### 1.1 PROJECT DESCRIPTION:

> As a full stack software developer you've been tasked with the development of an webapp for
> cinephiles and movie hobbyists. This first version (MVP) of the app will be very simple and
> limited to showing the list of upcoming movies. The app will be fed with content from The Movie
> Database (TMDb). No design specs were given, so you're free to follow your UX and UI
> personal preferences. The front-end for this app should be simple, so try to keep all the
> business logic on the back-end, including access to the TMDb API. As mentioned before, all
> data must come from TMDb, but feel free to create your own data storage strategy.

### 1.2 FUNCTIONAL REQUIREMENTS

These were the functional requirements:

> The first release of the app will be very limited in scope, but will serve as the foundation for
future releases. It's expected that user will be able to:
> * See a list of upcoming movies - including the movies' name, poster or backdrop image,
>   genre and release date. The list should not be limited to only the first 20 movies as
>   returned by the API.
> * Select a specific movie to see its details (name, poster image, genre, overview and
>   release date).
> * Search for movies by entering a partial or full movie name.

### 1.3 TECHNICAL REQUIREMENTS


> You should see this project as an opportunity to create an app following modern development
> best practices , but also feel free to use your own app architecture preferences (coding
> standards, code organization, third-party libraries, etc).
> 
> [...]
> 
> You can use any combination of frontend and backend technology
> You should create your own backend API layer, which will be responsible to send
> requests to the TMDb API
> The frontend app should request data only from your backend API
> Feel free to use any package/dependency managers if you see fit

### 1.4 NOTES

> Here at ArcTouch we're big believers of collective code ownership, so remember that you're
> writing code that will be reviewed, tested and maintained by other developers. Things to keep in
> mind:
> 
> * First of all, it should compile and run without errors
> * Be as clean and consistent as possible
> * Despite the project's simplicity, don't ignore development and architecture best practices.
> 
> It's expected that you choose a code architecture that encourages clear separation of concerns
> and supports project growth, but try not to over engineer. You should try to balance project
> simplicity and yet demonstrate you care about and understand code architecture best practices.
> This project description is intentionally vague in some aspects, but if you need assistance feel
> free to ask for help. We wish you good luck!

## 2. MINDSET

So the project (as expected) has a front and backend that are almost fully independent (it is if you change the engine that the front aims to another compatible one, for instance). Considering section 1 this allows me to use non-related programming languages, taking the best of each one on each side.

I'm considering two docker coantainers - one for front and another for backend - running a stock [PHP 7.3-fpm](https://github.com/docker-library/php/blob/85b2af63546309c3c7b895524db10ef02aa4edba/7.3/stretch/fpm/Dockerfile) and a [Node.js 8.16-jessie](https://github.com/nodejs/docker-node/blob/a8dbfa5c7cac9dca9145c6f429cd2c4f11176707/8/jessie/Dockerfile) dockerfile, for faster prototyping without losing portability among several developers. This must be reviewed before full production usage.

I could use just node for both sides (using some frontend framework like Vue.js or React), but for familiarity I'd choose a simple html+js for front (in a PHP server for further implementations, like sessions, login and stuff like that).

During the first tryouts with the docker image, I found myself struggled on hosting containers for free. I'm having some really rough days as I'm transictioning from my current job right now, and as I'm currently the leader of a 5-members DevOps team and have to finish ongoing projects before leaving (with the deadlines shorter now). From this limitation, and currently giving up the Docker thing and will just make a front and backend using JS+PHP, making CURL calls to the TMDb API and using some framework for HTML+CSS frontend (like Bootstrap).

I'll assume two completly different root folders for both sides, as the intention is to make it as independently as possible, and will also talk them on distinct sections.

## 3. FRONTEND

I thought on a minimalist design with a navbar (for searching) and the content, having a title and the movies list. If there's a search term, the content should change for the search results. If not, it should go back to the upcoming movies.

I have considered to different containers (one for search and another for upcoming) so that if an user cancels a search (for example by cleaning the search input box), the code should only display the upcoming container again (withou having to make a new request).

This made me duplicate some pieces of code, but it also allowed me to make different behaviors for each type of request, for example.

### 3.1 THIRD PARTY CODE

For rapid prototyping I did choose the combination of [Bootstrap 4](https://getbootstrap.com/) with [jQuery](https://jquery.com/). That way I can create a responsive layout for most modern browsers without spending a lot of time on CSS code. Both are loaded from CDN in order to reduce bandwidth usage.

Besides the thid party codes used above, I did also use a favicon from another source. I got it from [Favicon.io](https://favicon.io/), which is free tool for fast favicon prototyping. I did choose the `movie-camera` icon as it fits better in this project, IMO. It can be seen [here](https://favicon.io/emoji-favicons/movie-camera/).

![Movie Camera Icon](data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFT0lEQVRYR+2WW0xUVxSG/3XO3Lg43EQQURC5yWVmYHRGBE2tjYmJPhhjNWn7Yow+tW+9pE360Je2D31oY1Jbk7ZpE5OqjdG2adV6KQwgM5iZ0SIioNwpKgOiAsNw9mrOERCcEca2iWnS/bTP3muv9e211/73ITznRs85Pv6TAGSxVzkkHawA9AzRem/AUN3RcXH872TzmTJgta/fQrL0GREVzAnGuCuAD/zumoMA+FlAogawOCr3ySQdAkhOTk2FOTEZJEl4eH8EgwP9UBQFLPhLn8d14F8HKHE6LTrWe3R6vSG/xIp4c8KcGBPBcdz4w4/RBw8YLPb6PHXfRAsRVQZsjqqjRLQrt6gUSYtTI/oOjo/hamMDhBAdvoaaVaVr1m+XSd5LxMUghJi5gYX41N9Y753tIBoA2easGjaZYuJL11aAiJCUmIAtmzfBaDSi2lWPjq4uzWd7cxMCdwbAzL8T0UYApNPpIJghFEU1UYTgt/0e1yfTEAsCWCzrl8gx8kBiymLkFVu0dRsrK1CYn6f1h4aHcezEKa3f392JnlvtWj8ufhGy8gsRGxevfd8LDKKj9TomgkFmYKff7Tqhji8IYLPZEmGID5iTkqnQUqY5s1lK4LCXa/3Orm6cPndB66vBVQiD0YhiuxPq7jOWpiMYnMBgIIDRB/dxzduoFmuz111TFBWAFtCx4YYsS3lWZyV0er12DKsL8mAwGNF8vQXBiQk17bjm9aiFiGVZOcjIysZ6pwMlRYXa3PnqGrTf7NCKdXjwLgdDYmWzt65zwQxMAbxFhI9T0pZiZX6hBvBku93fi87WFm04p7AIKUvSsXvnDiSYF2ljrW3tuFBTi862G7jd1wMWisPnqfNEBZCdnW1KXJJ5iYisyalpWJ6zCgajSXOsTE7iz95u9HXemmFauiIbmdk5KLdaYC+zQgjGmXPn0d3bh+tXvBgZCvBYiDNbvLV9UQGongvKKjNi9HSSiNao36bYWEgkYWxsFCzEnISox1Rc7tBqIcFsRmgyhNHRMYwMD6HlihfM8PjcNQ6tBkpKnGm6OH0DM3/kd9d+EUlKbWvWVULWvQfASqCMsPxHGDCaYrB8VS4SEpNVbUDgzm303GpTFVOZFMqLVz111RpAWVllBgxSr/rB4N+CE2KfWhzTPq32yk0k069EZIgm8Hw2zHioKGL/1cu1R6bt5gBog8wjzHjT53EdVr/KHFX1IFoXybGqDbIkz8s1eGdAmxfMpycV7G+67HqkWlMtHGBqglmcIeLDzNIRItJHirJ992szQhNpXr1+R78+NLUvPuJzu14pKXda9HpZU7GhAcPPTwWIJt0qgN5gwM2W5jDzFTm5MMXEzgCA2S+Ab4mwnUAvaAsmxLJ/DKD6+fH778IANm/bgZTUtMcAkXb0P8BCGWBmpindLbLZkZ1bgP6eLngvubSEqjUQ7REw4ycKiQPQ0/sM7FfXUYgzn1IDPMLi0VW0OaoUc0ISbd25Z+YNuPDLKajarwKofGdP/RB2wlUvbUVSyuLZNXDc21Cz60nDMAAWfDYksG/6vtocVSI2Lo627XoVkixrL9uZk8cxHLirAUy/91Fcw2M+t+vleQAe73q2HJc5NvSDkJ6euQJZOXno7+lE1802zY+9YgMMpkePUsTGQP3Fs9qUYBz0u2teDwPIdTrNi1j/7nhIfD5bgqcNrWurPpQkeicaXZjHRmGhVKjPbxjAQo6Li4sN+rgk9S93z3RBLrRm9ryq/yz4DX+j66tI66J+jkvtFYWSLK+WSEjRArBCY6ExcampqT7wtDVRA0Qb9FntnjvAX1qYVVoftJWTAAAAAElFTkSuQmCC)

### 3.2 CREATED FILES

The frontend files are listed below. All the variables, functions and event listeners are described inside each file.

`index.html`: Main frontend file. It consists on a [navbar](https://getbootstrap.com/docs/4.3/components/navbar/) with a text input and a search button, a content title (to inform if showing search results or upcoming movies) and the content container itself. It also has a hidden item card that JS use as model for displaying backend results. There's a modal for displaying a single movie info, that is loaded through the movie ID stored on `data-value` attribute of the anchor tag button.

`app.js`: Main frontend code. 

`custom.css`: Small CSS changes to fix layout.

## 4. BACKEND

As discussed on section 2, the backend will be PHP based. For handling the endpoints, I'm assuming an [Apache](https://www.apache.org/) web server with [mod_rewrite](https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html) active, having a .htaccess file that redirects any request to a single PHP file for endpoint validation and routing, but this redirect could be done also on [nginx](https://www.nginx.com/) too (using [rewrite rules](https://www.nginx.com/blog/creating-nginx-rewrite-rules/)). This is the setup I have for hosting at the moment.

This is the code for the `.htaccess` file:

```
#Rewrite the URL to the router
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ router.php?request_url=$1 [L,QSA]
```

It basically checks if the file (or folder) requested doesn't exists, and if not it redirects to `router.php` file passing the URL (relative to the backend root) to the script as a `GET` param. I found a small bug during it's experimentation: if you try to access the backend root folder without any endpoint, it still retrieves a 404 for the index.php file (instead of calling the `router.php` file with an empty URL param). I created then an `index.php` file which is basically a small manual for the API use, describing the endpoints.

### 4.1 THIRD PARTY CODE

No third party libraries/classes were used on the backend of this project. It was all based on built-in Apache/PHP functions.

The only third party item on the backend folder is the favicon for the `index.php` file. It's the same from section 3.1.

### 4.2 CREATED FILES

The backend files are listed below. All the variables, functions and event listeners are described inside each file.

* `index.php`: Small manual with avaliable endpoints and live demo link;

* `.htaccess`: Apache rules for endpoint redirect;

* `router.php`: Endpoint router (redirected from `.htaccess`), which calls the class functions;

* `class/EndpointHandler.php`: Main endpoint handling class;

* `class/HttpHandler.php`: cURL requests handling class.


## 5 LIVE DEMO

There's a live demo of this project hosted [here](http://me.inf.br/arctouch/frontend). The backend is hosted [here](http://me.inf.br/arctouch/backend).