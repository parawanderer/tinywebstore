## Running

#### Prerequisites:
- docker
- [docker-compose](https://docs.docker.com/compose/install/) (not really necessary but I don't like typing long commands)
- [composer](https://getcomposer.org/download/) (not sure if really necessary, but I believe since we use a volume mount we may not pull dependencies in the container? I don't really feel inclined to check the container setup for the xamp container)


#### Running:

Ensure there is a `./codeigniter/.env` file, derived from [`./codeigniter/env`](./codeigniter/env).
The bare minimum are these properties being set:

```
database.default.hostname = localhost
database.default.database = app
database.default.username = root
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

Then from the root folder:

```
cd ./codeigniter
composer install
```

In the root folder:

```
docker-compose build
```

```
docker-compose up
```

#### Initialisation /Seeding

To initialise the database with some basic test data, send a get request to `/seed`
or use the command line seeder as [described here](https://codeigniter4.github.io/CodeIgniter4/dbmgmt/seeds.html#command-line-seeding)

The seeder name is `AppSeeder`. So e.g.
```
php spark db:seed AppSeeder
```

## Test Accounts (seeded)

#### Regular user account:
```
Username: bob@test.test
Password: myPassword123
```

#### Store owner 1:
```
Username: store@test.test
Password: anotherPasswrod323
```

#### Store owner 2:
```
Username: gsms@test.test
Password: anotherPasswrod323
```

#### Store owner 3:
```
Username: electronica@test.test
Password: anotherPasswrod323
```

#### Store owner 4:
```
Username: tvs@test.test
Password: anotherPasswrod323
```

### Disclaimers:

As this is a toy project, I did not do some things that I would have done in a real project.
This includes:

- Offloading long running processes to background process
- Long polling instead of regular polling for "alerts" (though it is debatable if it is needed in this sort of app, since this sort of app is not generally going to have a need to poll very frequently in rapid succession. The bare-bones polling here may honestly be the best implementation we could get)
- Messaging to be a modern websocket based, live system. Websocket wasn't covered in the course, and it's the go-to implementation for browser-based (or even more generally any) live messaging system.
- Alternative to above: Messaging via polling; I decided the messaging system will be more like in-app emails for ease (think forum accounts internal "mailing" system that does not actually go via email, such as in e.g. Reddit or various forum platforms. They have messaging but no real-time feedback messaging). But I choose to follow the layout of a messaging app (somewhat) for the messages. So I suppose this is a design oversight.
- Alerts for messages (see above comment, although the existing implementation for alerts could easily allow extension of new message received alerts. See `EventHelper.php`)
- The design/layout is very inspired by bol.com. Anything that isn't clearly inspired by bol.com I thought up on the spot. (Although one might argue long-time inspiration that I can't explicitly put a finger on. I also opened google images for examples of "web store stat pages")
- External libraries (and even an external executable) were added to the container/project to support "advanced" description building: [HtmlPurifier](http://htmlpurifier.org/) and [ffmpeg](https://ffmpeg.org/) and a [PHP library to interact with ffmpeg php-ffmpeg/php-ffmpeg](https://packagist.org/packages/php-ffmpeg/php-ffmpeg)
- External JS libraries used: show 2 charts on the shop stats page [chart.js](https://www.chartjs.org/), provide a WYSIWYG editor [pell](https://github.com/jaredreich/pell) on 2 pages (shop page edit, product edit). Former chosen since I liked the look, latter chosen for small size/simplicity
- Optimizing JS/HTML/CSS file size through "compressing" files (obviously this would be preferred in a real app)
- Caching (this app, if it were a real project, really needs it in some places. For a stateful tier-3 app like this you could generally get away with some sort of internal map structure... but not with PHP. Using text-files in whatever data format [PHP serialize/JSON] I really dislike, neither is it scalable. Nor do I think the point of this course is architecture to get into memcached or redis or something. So I didn't do it)
- I didn't actually use legacy AJAX via XMLHTTPRequest like was covered in this course. I don't believe the course addressed the new fetch API that is considered preferred. [I generally use this, it's cleaner and has more features.](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch#:~:text=Fetch%20provides%20a%20better%20alternative)
- Generally I did not go out fully of my way to write widest supported javascript, although I didn't exactly write modern javascript either. I would argue we should be allowed to write modern ecmascript client code rather than widest supported because often newer versions of the language include features useful to developers, and it's hard to mentally keep track of supported things in browsers or ruins efficiency to constantly look this up and still write poorer quality, less readable code due to it. Examples that benefit ecmascript code: modules, no global variables, let/const, various utility syntactical sugar like foreach loops, .... My preference is to writing in more recent ecmascript standards that have modern improvements, and "compiling" down to support the desired lowest "level" later, during the minification step that you would generally always want to do anyway. Of course if I really _needed_ to avoid the "compilator" (really a "converter") expanding the javascript massively, like could happen with e.g. fetch or async/await when compiling down very far back, then I'd write manual bits of code. But I generally don't see why you would want to do this in every case. Regardless, what I did try to do here is use mostly old ecmascript features and follow a consistent (old) style, such as full `function() {}` lambdas and `fori` loops, generally putting everything into functions (I believe I only created a class once?). So I guess I achieved "mostly old, at least consistent vanilla JS".
- I didn't minimise my CSS/JS. I believe the course touched on this topic. In a real project I would have done so, even if I didn't use an extensive compilator. In this project it would just make the code harder to follow for any potential reviewers.
- My transactions are wacky, I didn't put _that much_ thought into them, there could be issues. Though I don't think this is a focus of the course. They should be good enough for all intents and purposes of a small tier-3 live app. Intuitively there shouldn't be any basic race conditions or deadlock opportunities in most cases.
- The app only really supports mobile (sub 576px ish) and 1920×1080 screens. I styled it on a 4k screen. I only tested it on the chrome emulator. There could be issues. It certainly looks bad on the chrome emulator for "tablet" sizes.
- Session management/multi-session management I didn't do anything about. In a real app you may want to manage sessions in a more complex way, particularly for "store owner" accounts where you'd like the owner to be able to log out external sessions for security reasons.
- A real monetary-transaction app should have critical action logs. It should generally store more details for critical actions on the part of shop owners (security info), associate IPs/timestamps/details to critical actions in some kind of log. 
- A real app would typically want to associate a store to more than one user. Users may want to still have their individual user accounts, too. This app glues user accounts and stores together, and removes the messaging feature from store-users due to an implementation detail. This is purely because it is a simplification.
- Soft deletes to allow rollback, particularly on store items being deleted by a store. Hard deletes are rarely a great idea.
- Deleting data, e.g. by deleting users for GDPR. Obviously no in-built feature, but the app is mostly written to support dealing with deleted data in most places (deleted images/deleted products was the focus, but it's not extremely deep), except users. Usually concerning deleted store items.
- Having to save a new product before adding images is annoying UI-wise. It's somewhat a consequence of the barebones traditional HTML forms and endpoint reuse I've done here. This is very much a tradtional website like you'd see in 2017 and not a webapp.
- Actual verification of registered emails via email/a mail server. Of course I could have made an automatic verification system using expiring keys and email links, but it's additional mess and requires linking an external mail server. So I didn't do it. 
- Many more advanced features I didn't write. This project is really bare-bones in my opinion, compared to what a real small app should be. (I do think it satisifies the project description given pretty well)
- I also didn't add unit tests and such. I think they are way out of scope for such a small app, even if it was a real project, unless I had some advanced procedures I really wanted to assert the behaviour of (which I maybe do here, but most of the code here isn't in my opinion)

- ~~Optimizing uploaded files/compressing uploaded files/incoming request body limits. Although the actual implementation does not itself provide any custom assets outside of user-uploaded files, just using bootstrap assets that are already optimized.~~ I decided to mostly implement this with "backwards support" since it was the most major complaint of Chrome "Lighthouse" app review for mobile, since I saw the opportunity to fit in a replacement for media identifier lookup reuse. See the `MediaFile.php` class. Not my favourite implementation ever, but it works. The issues I faced implementing this/replacing this throughout the project really go to show that having unit tests in even such a small project has its uses, especially with PHP's lack of real typing and the reliance on arrays (in no part caused by my usage of arrays, but also a fundamental aspect of how the language started off since at least 5.x to my knowledge)