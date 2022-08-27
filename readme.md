## Running

Prerequisite 
- docker
- [docker-compose](https://docs.docker.com/compose/install/) (not really necessary but I don't like typing long commands)
- [composer](https://getcomposer.org/download/) (not sure if really necessary, but I believe since we use a volume mount we may not pull dependencies in the container? I don't really feel inclined to check the container setup for the xamp container)


Run:

#### Dependencies:
```
cd ./codeigniter
composer install
```

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
- Long polling instead of regular polling for "alerts" (though it is debatable if it is needed in this sort of app)
- Messaging to be a modern websocket based, live system. Websocket wasn't covered in the course.
- Messaging via polling: I decided the messaging system will be more like in-app emails for ease
- Alerts for messages (see above comment)
- The design/layout is very inspired by bol.com. Anything that isn't clearly inspired by bol.com I thought up on the spot.
- External libraries (and even an external executable) were added to the container/project to support "advanced" description building: [HtmlPurifier](http://htmlpurifier.org/) and [ffmpeg](https://ffmpeg.org/) and a [PHP library to interact with ffmpeg php-ffmpeg/php-ffmpeg](https://packagist.org/packages/php-ffmpeg/php-ffmpeg)
- External JS libraries used: show 2 charts on the shop stats page [chart.js](https://www.chartjs.org/), provide a WYSIWYG editor [pell](https://github.com/jaredreich/pell). Former chosen since I liked the look, latter chosen for small size/simplicity
- Optimizing JS/HTML/CSS file size through "compressing" files (obviously this would be preferred in a real app)
- Caching (this app, if it were a real project, really needs it in some places. For a stateful tier-3 app like this you could generally get away with some sort of internal map structure... but not with PHP. Using text-files in whatever data format (PHP serilize/JSON) I really dislike. Nor do I think the point of this course is architecture to get into memcached or redis or something. So I didn't do it)
- I didn't actually use legacy AJAX via XMLHTTPRequest like was covered in this course. I don't believe the course addressed the new fetch API that is considered preferred. [I generally use this, it's cleaner and has more features.](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch#:~:text=Fetch%20provides%20a%20better%20alternative)
- Generally I did not go out of my way to write widest supported javascript, although I didn't exactly write modern javascript either. I would argue we should be allowed to write modern ecmascript client code rather than widest supported because often newer versions of the language include features useful to developers, and it's hard to mentally keep track of supported things in browsers or ruins efficiency to constantly look this up and still write poorer quality, less readable code due to it. Say modules, no global variables, let/const, various utility syntactical sugar like foreach loops, .... My preference is to writing in more recent ecmascript standards that have modern improvements, and "compiling" down to support the desired lowest "level" later, during the minification step that you would generally always want to do anyway. Of course if I really _needed_ to avoid the "compilator" (really a "converter") expanding the javascript massively, like could happen with e.g. fetch or async/await when compiling down very far back, then I'd write manual bits of code. But I generally don't see why you would want to do this in every case. Now I've digressed. But what I did try to do here is use mostly old ecmascript features and follow a consistent (old) style, such as full function() {} lambdas and fori loops, generally putting everything into functions (I believe I only created a class once?).
- My transactions are wacky, I didn't put _that much_ thought into them, there could be issues. Though I don't think this is a focus of the course. They should be good enough for all intents and purposes of a small tier-3 live app.
- The app only really supports mobile (sub 576px ish) and 1920×1080 screens. I styled it on a 4k screen. I only tested it on the chrome emulator. There could be issues. It certainly looks bad on the chrome emulator for "tablet" sizes.
- Session management/multi-session management I didn't do anything about. In a real app you may want to manage sessions in a more complex way, particularly for "store owner" accounts where you'd like the owner to be able to log in external sessions for security reasons
- A real transaction app should have transaction logs, store more details for critical actions on the part of shop owners (security info), associate IPs/timestamps/details to critical actions in some kind of log. 
- A real app would typically want to associate a store to more than one user. Users may want to still have their individual user accounts, too. This app glues user accounts and stores together, and removes the messaging feature from store-users due to an implementation detail. This is purely because it is a simplification.
- Soft deletes to allow rollback, particularly on store items being deleted by a store
- Deleting data, e.g. by deleting users for GDPR. Obviously no in-built feature, but the app is mostly written to support dealing with deleted data in most places (deleted images/deleted products was the focus, but it's not extremely deep), except users. Usually concerning deleted store items.
- Actual verification of registered emails via email/a mail server. Of course I could have made an automatic verification system using expiring keys and email links, but it's additional mess and requires linking an external mail server. So I didn't do it. 
- Many more advanced features I didn't write... This project is really bare-bones in my opinion.
- I also didn't add unit tests and such. I think they are way out of scope for such a small app, even if it was a real project, unless I had some advanced procedures I really wanted to assert the behaviour of (which I maybe do here, but most of the code here isn't in my opinion)