alter table FEED Add column totalCount mediumint(7) default '0';
alter table FEED Add column guestRating mediumint(7) default '0';
alter table FEED Add column guestRatingCount mediumint(7) default '0';
alter table FEED Add column image varchar(500);

alter table FEED Add column viewCount mediumint(7) default '0';
alter table FEED Add column fblog mediumint(7) default '0';
alter table FEED Add column fbCount mediumint(7) default '0';
alter table FEED Add column likes mediumint(7);
alter table FEED Add column shares mediumint(7);
alter table FEED Add column charCount SMALLINT(6) unsigned;

alter table FEED modify column url VARCHAR( 512 ) NOT NULL unique;
alter table `www.skcript.com` modify column id mediumint unsigned not null AUTO_INCREMENT primary key;

update FEED set fblog=NULL where fblog=11;


alter table  `humanheed.com` Add column normMoz mediumint;
create table users(email varchar(60), website varchar(60));

create table `skcript.com`(id mediumint unsigned AUTO_INCREMENT primary key, title varchar(255),category varchar(20), keywords varchar(512),url VARCHAR( 512 ) NOT NULL unique, image VARCHAR( 812 ), author VARCHAR( 30 ), date DATE, source TINYINT unsigned,fblikes mediumint unsigned, fbshares mediumint unsigned,mozPa TINYINT unsigned, total mediumint unsigned, fbCount mediumint default '0' unsigned,reddit MEDIUMINT unsigned, pinterest MEDIUMINT unsigned,charCount MEDIUMINT unsigned,wordCount MEDIUMINT unsigned, sentCount SMALLINT unsigned,titleCount TINYINT unsigned,imgCount TINYINT unsigned,stopWords SMALLINT unsigned,entropy TINYINT unsigned);
#Can drop description to reduce db cost and for better ui. If needed, "description varchar(3000)".

alter table  `techcrunch.com` Add column EthereumAddress varchar(100);