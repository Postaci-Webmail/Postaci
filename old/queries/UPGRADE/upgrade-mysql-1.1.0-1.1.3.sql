alter table tblUsers add rsrv_int1 int;
alter table tblUsers add rsrv_int2 int,
alter table tblUsers add rsrv_int3 int;
alter table tblUsers add rsrv_int4 int;
alter table tblUsers add rsrv_int5 int;
alter table tblUsers add rsrv_int6 int;
alter table tblUsers add rsrv_char1 varchar(255);
alter table tblUsers add rsrv_char2 varchar(255);
alter table tblUsers add rsrv_char3 varchar(255);
alter table tblUsers add rsrv_char4 varchar(255);
alter table tblUsers add rsrv_char5 varchar(255);
alter table tblUsers add rsrv_char6 varchar(255);
alter table tblUsers add rsrv_text1 text;
alter table tblUsers add rsrv_text2 text;
alter table tblUsers add rsrv_text3 text;
alter table tblUsers add rsrv_text4 text;
alter table tblUsers add rsrv_text5 text;
alter table tblUsers add rsrv_text6 text;

alter table tblLoggedUsers add rsrv_int1 int;
alter table tblLoggedUsers add rsrv_int2 int;
alter table tblLoggedUsers add rsrv_char1 varchar(255);
alter table tblLoggedUsers add rsrv_char2 varchar(255);

alter table tblUsers change column password password varchar(100);
alter table tblUsers change column username username varchar(100);

alter table tblUserDomains change column username username varchar(100);
alter table tblMailBoxes change column mboxname mboxname varchar(100);
