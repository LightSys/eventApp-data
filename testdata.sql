insert into event(ID, name,refresh,refresh_expire,time_zone,welcome_message,logo) values (UUID(),"hello",60,CURDATE(),"GMT-04:00", "hello world", x'12abcdef');
insert into contact_page_sections(event_ID,header,content) values (1,"Hello","world");
insert into contacts(event_ID,name,address,phone) values (1,"Josiah the great","home","(000) 000-0000");
