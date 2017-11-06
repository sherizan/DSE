# DSE
CSCI 311 - Distributed Search Engine

## How to install (Windows)
1. `git clone repo` to /dse
2. [Download](https://www.apachefriends.org/index.html) and install XAMPP
3. Make a new folder in `xampp/htdocs/` called `dse/`
4. Open `xampp/htdocs/index.php`, change `header('Location: '.$uri.'/dashboard/');` to `header('Location: '.$uri.'/dse/');`
5. Copy over contents of `Frontend/` into `xampp/htdocs/dse/`
6. Open XAMPP Control Panel and start Apache
7. Open your browser and go to `localhost`, it should open the DSE page

## How to build (Windows)
1. `git clone repo` to /dse
2. Open `Backend/CSCI 311 DSE.sln` in Visual Studios (2015 and above).
3. Build in Release
4. Copy over `Deliverables/config.ini` into `Backend/Crawler/bin/Release/`
5. Edit `Backend/Crawler/bin/Release/config.ini` with desired search path and desired db save location (default save location is same directory as executable)

## Members:
* Sherizan (Project Lead/Front End)
* Zai Hao (Front End)
* George (Back End)
* Mon (Testing)
* Hui Lim (Documentation)

## Frontend
The frontend consists of two parts: UI and Queries.

##### User Interface
- The UI is written using HTML and CSS that uses an open source framework called Bootstrap.

##### Queries
- The search queries are written in SQL and connected to SQLite.
- The connection from SQLite to PHP uses a dependancy manager, Composer (https://getcomposer.org)
- The programming language is written in PHP which is server-side.

## Backend
The Crawler is written in C# and contains four main parts.
##### Crawler
- Goes through all files and folders (and inner folders) in the given directory.
- Creates a new thread to parse the file.
##### Parser
- Goes through the file and decides if the file can be parsed (is text based)
- Seperate words according to whitespace
- Normalize the words, e.g. de-capitalizing, removing punctuation
- Passes on word entries and corresponding file name to Indexer
##### Indexer
- Creates `.sqlite` database file
- Starts and holds connection to database
- Has a seperate thread to queue and group `INSERT` commands
- Formalizes word-file links into SQL statements
##### Console
- Shows progress of Crawler and Indexer
