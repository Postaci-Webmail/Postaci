CREATE TABLE tblSpamList (
  item_id bigint(20) unsigned not null auto_increment,
  user_id bigint(20) unsigned not null,
  email varchar(50),
  spam_date date,
  PRIMARY KEY (item_id)
);

CREATE TABLE tblAdressbook (
  item_id bigint(20) unsigned NOT NULL auto_increment,
  user_id bigint(20) unsigned NOT NULL,
  real_name varchar(100) DEFAULT '----',
  email1 varchar(50),
  notes varchar(255),
  telephone varchar(15),
  PRIMARY KEY (item_id)
);

CREATE TABLE tblFavorites (
  favorite_id bigint(20) unsigned NOT NULL auto_increment,
  user_id bigint(20) unsigned NOT NULL,
  url text,
  url_title varchar(255) DEFAULT '----' NOT NULL,
  notes varchar(255),
  PRIMARY KEY (favorite_id)
);

CREATE TABLE tblNotebook (
  note_id bigint(20) unsigned NOT NULL auto_increment,
  user_id bigint(20) unsigned NOT NULL,
  notetitle varchar(255) DEFAULT '----' NOT NULL,
  notes blob,
  note_date varchar(20) DEFAULT '' NOT NULL,
  PRIMARY KEY (note_id)
);

create table tblUsers (
  user_id bigint unsigned NOT NULL auto_increment,
  username varchar(100) NOT NULL,
  password varchar(100) NOT NULL,
  real_name varchar(100),
  domain_id int NOT NULL,
  last_visit date NOT NULL,
  last_ip varchar(15),
  pop3_count bigint,
  imap_count bigint,
  signature text,
  rsrv_int1 int,
  rsrv_int2 int,
  rsrv_int3 int,
  rsrv_int4 int,
  rsrv_int5 int,
  rsrv_int6 int,
  rsrv_char1 varchar(255),
  rsrv_char2 varchar(255),
  rsrv_char3 varchar(255),
  rsrv_char4 varchar(255),
  rsrv_char5 varchar(255),
  rsrv_char6 varchar(255),
  rsrv_text1 text,
  rsrv_text2 text,
  rsrv_text3 text,
  rsrv_text4 text,
  rsrv_text5 text,
  rsrv_text6 text,
  PRIMARY KEY (user_id)
);

create table tblUserDomains (
  username varchar(100) NOT NULL,
  domain_id int NOT NULL,
  PRIMARY KEY (username)
);

create table tblLoggedUsers (
  log_id bigint unsigned NOT NULL auto_increment,
  username varchar(100) NOT NULL,
  password varchar(100) NOT NULL,
  hash varchar(255) NOT NULL,
  ip varchar(15),
  connect_date date NOT NULL,
  connect_time varchar(5) NOT NULL,
  user_id bigint unsigned NOT NULL,
  rsrv_int1 int,
  rsrv_int2 int,
  rsrv_char1 varchar(255),
  rsrv_char2 varchar(255),
  PRIMARY KEY (log_id)
);

CREATE TABLE tblDomains (
  domain_id int unsigned NOT NULL auto_increment,
  domain varchar(125) NOT NULL,
  PRIMARY KEY (domain_id)
);

create table tblMailBoxes (
  mbox_id bigint unsigned NOT NULL auto_increment,
  user_id bigint unsigned NOT NULL,
  mboxname varchar(15) NOT NULL,
  mbox_type int DEFAULT '3' NOT NULL,
  PRIMARY KEY (mbox_id)
);

create table tblMessages (
  message_id bigint unsigned NOT NULL auto_increment,
  mbox_id bigint not null,
  user_id bigint not null,
  header_from varchar(255),
  header_to varchar(255),
  header_cc varchar(255),
  header_replyto varchar(255),
  header_date varchar(100),
  header_subject varchar(255),
  header_size varchar(40),
  msg_body blob,
  PRIMARY KEY (message_id)
);

create table tblAttachments (
  attach_id bigint unsigned NOT NULL auto_increment,
  message_id bigint not null,
  user_id bigint NOT NULL,
  file_type varchar(50),
  file_name varchar(255),
  file_actual_name varchar(255),
  PRIMARY KEY (attach_id)
);

create table tblMIME (
  mime_type varchar(100) not null,
  mime_ext varchar(10) not null
);

insert into tblMIME values('application/mac-binhex40','hqx');
insert into tblMIME values('application/mac-compactpro','cpt');
insert into tblMIME values('application/msword','doc');
insert into tblMIME values('application/octet-stream','bin');
insert into tblMIME values('application/octet-stream','dms');
insert into tblMIME values('application/octet-stream','lha');
insert into tblMIME values('application/octet-stream','lzh');
insert into tblMIME values('application/octet-stream','exe');
insert into tblMIME values('application/octet-stream','class');
insert into tblMIME values('application/oda','oda');
insert into tblMIME values('application/pdf','pdf');
insert into tblMIME values('application/pgp-encrypted','pgp');
insert into tblMIME values('application/pgp-keys','pkr');
insert into tblMIME values('application/postscript','ai');
insert into tblMIME values('application/postscript','eps');
insert into tblMIME values('application/postscript','ps');
insert into tblMIME values('application/rtf','rtf');
insert into tblMIME values('application/smil','smi');
insert into tblMIME values('application/smil','smil');
insert into tblMIME values('application/vnd.ms-excel','xls');
insert into tblMIME values('application/vnd.ms-powerpoint','ppt');
insert into tblMIME values('application/x-bcpio','bcpio');
insert into tblMIME values('application/x-cdlink','vcd');
insert into tblMIME values('application/x-chess-pgn','pgn');
insert into tblMIME values('application/x-compress','Z');
insert into tblMIME values('application/x-cpio','cpio');
insert into tblMIME values('application/x-csh','csh');
insert into tblMIME values('application/x-director','dcr');
insert into tblMIME values('application/x-director','dir');
insert into tblMIME values('application/x-director','dxr');
insert into tblMIME values('application/x-dvi','dvi');
insert into tblMIME values('application/x-futuresplash','spl');
insert into tblMIME values('application/x-gtar','gtar');
insert into tblMIME values('application/x-gzip','gz');
insert into tblMIME values('application/x-gzip','tgz');
insert into tblMIME values('application/x-hdf','hdf');
insert into tblMIME values('application/x-javascript','js');
insert into tblMIME values('application/x-latex','latex');
insert into tblMIME values('application/x-netcdf','nc');
insert into tblMIME values('application/x-netcdf','cdf');
insert into tblMIME values('application/x-sh','sh');
insert into tblMIME values('application/x-shar','shar');
insert into tblMIME values('application/x-shockwave-flash','swf');
insert into tblMIME values('application/x-stuffit','sit');
insert into tblMIME values('application/x-sv4cpio','sv4cpio');
insert into tblMIME values('application/x-sv4crc','sv4crc');
insert into tblMIME values('application/x-tar','tar');
insert into tblMIME values('application/x-tcl','tcl');
insert into tblMIME values('application/x-tex','tex');
insert into tblMIME values('application/x-texinfo','texinfo');
insert into tblMIME values('application/x-texinfo','texi');
insert into tblMIME values('application/x-troff','t');
insert into tblMIME values('application/x-troff','tr');
insert into tblMIME values('application/x-troff','troff');
insert into tblMIME values('application/x-troff-man','man');
insert into tblMIME values('application/x-troff-me','me');
insert into tblMIME values('application/x-troff-ms','ms');
insert into tblMIME values('application/x-ustar','ustar');
insert into tblMIME values('application/x-wais-source','src');
insert into tblMIME values('application/zip','zip');
insert into tblMIME values('audio/basic','au');
insert into tblMIME values('audio/basic','snd');
insert into tblMIME values('audio/midi','mid');
insert into tblMIME values('audio/midi','midi');
insert into tblMIME values('audio/midi','kar');
insert into tblMIME values('audio/mpeg','mpga');
insert into tblMIME values('audio/mpeg','mp2');
insert into tblMIME values('audio/mpeg','mp3');
insert into tblMIME values('audio/x-aiff','aif');
insert into tblMIME values('audio/x-aiff','aiff');
insert into tblMIME values('audio/x-aiff','aifc');
insert into tblMIME values('audio/x-pn-realaudio','ram');
insert into tblMIME values('audio/x-pn-realaudio','rm');
insert into tblMIME values('audio/x-pn-realaudio-plugin','rpm');
insert into tblMIME values('audio/x-realaudio','ra');
insert into tblMIME values('audio/x-wav','wav');
insert into tblMIME values('chemical/x-pdb','pdb');
insert into tblMIME values('chemical/x-pdb','xyz');
insert into tblMIME values('image/bmp','bmp');
insert into tblMIME values('image/gif','gif');
insert into tblMIME values('image/ief','ief');
insert into tblMIME values('image/jpeg','jpeg');
insert into tblMIME values('image/jpeg','jpg');
insert into tblMIME values('image/jpeg','jpe');
insert into tblMIME values('image/png','png');
insert into tblMIME values('image/tiff','tiff');
insert into tblMIME values('image/tiff','tif');
insert into tblMIME values('image/x-cmu-raster','ras');
insert into tblMIME values('image/x-portable-anymap','pnm');
insert into tblMIME values('image/x-portable-bitmap','pbm');
insert into tblMIME values('image/x-portable-graymap','pgm');
insert into tblMIME values('image/x-portable-pixmap','ppm');
insert into tblMIME values('image/x-rgb','rgb');
insert into tblMIME values('image/x-xbitmap','xbm');
insert into tblMIME values('image/x-xpixmap','xwd');
insert into tblMIME values('model/iges','igs');
insert into tblMIME values('model/iges','iges');
insert into tblMIME values('model/mesh','msh');
insert into tblMIME values('model/mesh','mesh');
insert into tblMIME values('model/mesh','silo');
insert into tblMIME values('model/vrml','wrl');
insert into tblMIME values('model/vrml','vrml');
insert into tblMIME values('text/css','css');
insert into tblMIME values('text/html','html');
insert into tblMIME values('text/html','htm');
insert into tblMIME values('text/plain','asc');
insert into tblMIME values('text/plain','txt');
insert into tblMIME values('text/richtext','rtx');
insert into tblMIME values('text/sgml','sgml');
insert into tblMIME values('text/sgml','sgm');
insert into tblMIME values('text/tab-separated-values','tsv');
insert into tblMIME values('text/x-setext','etx');
insert into tblMIME values('text/xml','xml');
insert into tblMIME values('video/mpeg','mpeg');
insert into tblMIME values('video/mpeg','mpg');
insert into tblMIME values('video/mpeg','mpe');
insert into tblMIME values('video/quicktime','qt');
insert into tblMIME values('video/quicktime','mov');
insert into tblMIME values('video/x-msvideo','avi');
insert into tblMIME values('video/x-sgi-movie','movie');
insert into tblMIME values('x-conference/x-cooltalk','ice');
