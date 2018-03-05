create table event (
    ID                  varchar(36) UNIQUE,
    internal_ID         int AUTO_INCREMENT, 
    name                varchar(100),
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

    primary key (internal_ID)
);

create table contact_page (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    section_ID          int,
    header              varchar(100),
    content             varchar(1000) --?

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

create table contacts (
    ID                  int AUTO_INCREMENT,
    name                varchar(100),
    address             varchar(100),
    phone               varchar(17), --?
    event_ID            int,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

create table schedule_items (
    ID                  int AUTO_INCREMENT,
    date                date,
    start_time          numeric(4,0),
    length              int,
    description         varchar(150),
    location            varchar(50),
    category            varchar(50),
    event_ID            int,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

create table info_page (
    ID                  int AUTO_INCREMENT,
    nav                 varchar(25),
    icon                varchar(100),
    event_ID            int,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

create table info_page_sections (
    ID                  int AUTO_INCREMENT,
    title               varchar(50),
    description         text,
    info_page_ID        int,

    primary key (ID),
    foreign key (info_page_ID) references info_page(ID)
);

create table housing (
    ID                  int AUTO_INCREMENT,
    host_name           varchar(100),
    driver              varchar(100), --?
    event_ID            int,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

create table prayer_partners(
    group_ID            int AUTO_INCREMENT,
    event_ID            int,

    primary key (group_ID),
    foreign key (event_ID) references event(internal_ID)
);

create table attendees (
    ID                  int AUTO_INCREMENT,
    name                varchar(30),
    house_ID            int,
    prayer_group_ID     int,

    primary key (ID),
    foreign key (house_ID) references housing(ID),
    foreign key (prayer_group_ID) references prayer_partners(ID)
);

create table notifications (
    ID                  int AUTO_INCREMENT,
    event_ID            int,
    title               varchar(100),
    body                text,
    date                date,
    refresh             int,

    primary key (ID),
    foreign key (event_ID) references event(internal_ID)
);

-- How are themes and events related?
create table theme (
    ID                  int AUTO_INCREMENT,
    theme_name          varchar(50) UNIQUE,
    theme_color         varchar(7),

    primary key (ID)
);

create table users (
    ID                  int AUTO_INCREMENT,
    username            varchar(30) UNIQUE,
    password            varchar(98),

    primary key (ID)
);

create table event_users (
    ID                  int AUTO_INCREMENT,
    user_ID             int,
    event_ID            int,

    primary key (ID),
    foreign key (user_ID) references users(ID),
    foreign key (event_ID) references event(internal_ID)
);