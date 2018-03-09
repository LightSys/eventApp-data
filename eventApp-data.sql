-- Recreates database with event data

SET FOREIGN_KEY_CHECKS = 0;
drop table event;
drop table contact_page_sections;
drop table contacts;
drop table schedule_items;
drop table info_page;
drop table info_page_sections;
drop table housing;
drop table prayer_partners;
drop table attendees;
drop table notifications;
drop table themes;
drop table users;
drop table event_users;
SET FOREIGN_KEY_CHECKS = 1;

-- Contains general information about events and data needed
-- for an event's navigation menu
create table event (
    ID                  varchar(36) UNIQUE,
    internal_ID         int AUTO_INCREMENT, 
    name                varchar(100),
    year                numeric(4,0),
    refresh             int,
    refresh_expire      date,
    time_zone           varchar(9),
    welcome_message     varchar(100),
    notif_url           varchar(150),
    logo                blob,
    contact_nav         varchar(25),
    contact_icon        varchar(100),
    sched_nav           varchar(25),
    sched_icon          varchar(100),
    housing_nav         varchar(25),
    housing_icon        varchar(100),
    prayer_nav          varchar(25),
    prayer_icon         varchar(100),
    notif_nav           varchar(25),
    notif_icon          varchar(100),
    theme_dark          varchar(7),
    theme_medium        varchar(7),
    theme_color         varchar(7),
    visible             boolean,

    primary key (internal_ID)
) ENGINE = INNODB;

-- Contains information about themes
create table themes (
    ID                  int AUTO_INCREMENT,
    event_ID            int,    
    theme_name          varchar(50),
    theme_color         varchar(7),

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains information for laying out sections of the contact page
create table contact_page_sections (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    sequential_ID       int,
    header              varchar(100),
    content             text,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Stores contact information to populate contact page
create table contacts (
    ID                  int AUTO_INCREMENT,
    event_ID            int,    
    sequential_ID       int,
    name                varchar(100),
    address             varchar(100),
    phone               varchar(17),


    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains information to lay out a schedule
create table schedule_items (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    sequential_ID       int,    
    date                date,
    start_time          numeric(4,0),
    length              int,
    description         varchar(150),
    location            varchar(50),
    category            varchar(50),

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains information about housing arrangements
create table housing (
    ID                  int AUTO_INCREMENT,
    event_ID            int,    
    sequential_ID       int,
    host_name           varchar(100),
    driver              varchar(100),

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains information about the placement of people in prayer groups
create table prayer_partners(
    group_ID            int AUTO_INCREMENT,
    event_ID            int,
    sequential_ID       int,

    primary key (group_ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains the names of attendees and where they have been assigned
create table attendees (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    sequential_ID       int,
    name                varchar(30),
    house_ID            int,
    prayer_group_ID     int,

    primary key (ID),
    foreign key (house_ID) references housing(ID)
        on delete set null,
    foreign key (prayer_group_ID) references prayer_partners(group_ID)
        on delete set null
) ENGINE = INNODB;

-- Stores any information needed for a notification
create table notifications (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    title               varchar(100),
    body                text,
    date                datetime,
    refresh             boolean,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Defines a link on the nav bar for a user-defined page
create table info_page (
    ID                  int AUTO_INCREMENT,
    event_ID            int,    
    sequential_ID       int,
    nav                 varchar(25),
    icon                varchar(100),

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;

-- Contains information to lay out a user-defined page
create table info_page_sections (
    ID                  int AUTO_INCREMENT,
    info_page_ID        int,
    sequential_ID       int,
    header              varchar(100),
    content             text,

    primary key (ID),
    foreign key (info_page_ID) references info_page(ID)
        on delete cascade
) ENGINE = INNODB;

-- Stores usernames and hashed passwords
create table users (
    ID                  int AUTO_INCREMENT,
    username            varchar(30) UNIQUE,
    password            varchar(2048),

    primary key (ID)
) ENGINE = INNODB;

-- Associates users with events they have created or can access
create table event_users (
    ID                  int AUTO_INCREMENT,
    user_ID             int,
    event_ID            int,

    primary key (ID),
    foreign key (user_ID) references users(ID)
        on delete set null,
    foreign key (event_ID) references event(internal_ID)
        on delete cascade
) ENGINE = INNODB;