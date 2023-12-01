drop table facility cascade constraints;
drop table rent cascade constraints;
drop table venue cascade constraints;
drop table seat;
drop table event cascade constraints;
drop table genre cascade constraints;
drop table concert;
drop table play;
drop table conference;
drop table customertier cascade constraints;
drop table customer cascade constraints;
drop table transaction cascade constraints;
drop table ticketprice cascade constraints;
drop table ticketstatus;
drop table ticket;
drop table sponsor cascade constraints;
drop table sponsorship;
drop table staff cascade constraints;
drop table assignment;

create table facility
    (venuetype varchar(20),
    amenities varchar(100),
    primary key (venuetype));

create table rent
    (venuetype varchar(20),
    capacity int not null,
    venueprice int,
    primary key (venuetype, capacity),
    foreign key (venuetype) references facility on delete cascade);

create table venue
    (venueid int,
    venuename varchar(30),
    venueaddress varchar(30) not null,
    capacity int,
    venuetype varchar(20),
    unique (venueaddress),
    primary key (venueid),
    foreign key (venuetype, capacity) references rent on delete cascade);

create table seat
    (venueid int,
    seatnum varchar(30),
    seattype varchar(10),
    primary key (venueid, seatnum),
    foreign key (venueid) references venue on delete cascade);

create table event
    (eventid int,
    venueid int,
    datetime timestamp not null,
    eventname varchar(30),
    primary key (eventid),
    foreign key (venueid) references venue on delete cascade);

create table genre
    (performer varchar(30) primary key,
    genre varchar(30));

create table concert
    (eventid int,
    performer varchar(30),
    primary key (eventid, performer),
    foreign key (eventid) references event on delete cascade,
    foreign key (performer) references genre on delete cascade);

create table play
    (eventid int,
    director varchar(30),
    primary key (eventid, director),
    foreign key (eventid) references event on delete cascade);

create table conference
    (eventid int,
    host varchar(30),
    primary key (eventid, host),
    foreign key (eventid) references event on delete cascade);

create table customertier
    (loyaltypoints int primary key,
    tier varchar(20));

create table customer
    (custid int,
    custname varchar(30) not null,
    custaddress varchar(60),
    custphone varchar(20) not null,
    custemail varchar(50) not null,
    loyaltypoints int default 0,
    unique (custemail),
    primary key (custid),
    foreign key (loyaltypoints) references customertier on delete cascade);

create table transaction
    (transid int primary key,
    paymentmethod varchar(20),
    custid int not null,
    foreign key (custid) references customer on delete cascade);

create table ticketstatus
    (transid int primary key,
    status varchar(20),
    foreign key (transid) references transaction on delete cascade);

create table ticketprice
    (tickettype varchar(20),
    eventid int,
    ticketprice int,
    primary key (tickettype, eventid),
    foreign key (eventid) references event on delete cascade);

create table ticket
    (ticketid int,
    tickettype varchar(20),
    eventid int,
    transid int,
    primary key (ticketid, tickettype, eventid, transid),
    foreign key (tickettype, eventid) references ticketprice on delete cascade,
    foreign key (transid) references transaction on delete cascade);

create table sponsor
    (sponsid int primary key,
    sponsname varchar(40),
    sponsaddress varchar(50),
    sponsphone varchar(20),
    sponsemail varchar(50) not null,
    unique (sponsemail));

create table sponsorship
    (eventid int,
    sponsid int,
    fund int,
    primary key (eventid, sponsid),
    foreign key (eventid) references event on delete cascade,
    foreign key (sponsid) references sponsor on delete cascade);

create table staff
    (staffid int primary key,
    staffname varchar(30),
    staffaddress varchar(50),
    staffphone varchar(20),
    staffemail varchar(50) not null,
    unique (staffemail));

create table assignment
    (eventid int,
    staffid int,
    role varchar(30),
    primary key (eventid, staffid),
    foreign key (eventid) references event on delete cascade,
    foreign key (staffid) references staff on delete cascade);

insert into facility values ('conference room', 'audiovisual, wi-fi, seating, whiteboard, air-conditioning');
insert into facility values ('banquet hall', 'audiovisual, wi-fi, seating, stage, air-conditioning');
insert into facility values ('theatre', 'seating, sound, screen, concessions, accessibility');
insert into facility values ('arena', 'seating, sound, lighting, concessions, lockers, first aid station');
insert into facility values ('stadium', 'seating, sound, lighting, concessions, vip suites, sports bars');

insert into rent values ('conference room', 50, 200);
insert into rent values ('conference room', 200, 500);
insert into rent values ('banquet hall', 100, 500);
insert into rent values ('banquet hall', 500, 1000);
insert into rent values ('theatre', 500, 2000);
insert into rent values ('theatre', 1000, 5000);
insert into rent values ('arena', 10000, 5000);
insert into rent values ('arena', 30000, 20000);
insert into rent values ('stadium', 30000, 30000);
insert into rent values ('stadium', 50000, 50000);

insert into venue values (1, 'city arena', '123 main street', 10000, 'arena');
insert into venue values (2, 'hillside stadium', '456 elm street', 30000, 'stadium');
insert into venue values (3, 'grand theater', '789 oak avenue', 1000, 'theatre');
insert into venue values (4, 'elegant banquet hall', '222 rose lane', 100, 'banquet hall');
insert into venue values (5, 'downtown convention center', '101 maple drive', 50, 'conference room');

insert into seat values (1, 'a101', 'vip');
insert into seat values (1, 'a102', 'vip');
insert into seat values (2, 'section 1, row a, seat 1', 'standard');
insert into seat values (3, 'section d, seat 1', 'standard');
insert into seat values (3, 'section b, seat 10', 'vip');

insert into event values (1, 1, timestamp'2023-10-20 18:00:00', 'grand opening gala');
insert into event values (2, 2, timestamp'2023-11-15 14:30:00', 'music festival');
insert into event values (3, 3, timestamp'2023-12-05 19:00:00', 'shakespearean play');
insert into event values (4, 4, timestamp'2023-11-30 20:00:00', 'annual awards ceremony');
insert into event values (5, 5, timestamp'2023-10-25 10:00:00', 'tech conference');

insert into genre values ('taylor swift', 'pop');
insert into genre values ('coldplay', 'alternative');

insert into concert values (1, 'taylor swift');
insert into concert values (2, 'coldplay');

insert into play values (3, 'john smith');
insert into play values (4, 'sarah johnson');

insert into conference values (5, 'techcorp');

insert into customertier values (0, 'bronze');
insert into customertier values (500, 'silver');
insert into customertier values (1000, 'gold');
insert into customertier values (2000, 'platinum');
insert into customertier values (5000, 'diamond');

insert into customer values (2000, 'jane doe', '123 main street', '555-555-5555', 'janedoe@example.com', 0);
insert into customer values (3000, 'john doe', '123 main street', '555-123-4567', 'johndoe@example.com', 500);
insert into customer values (4000, 'alice smith', '456 elm road', '555-987-6543', 'alice@example.com', 1000);
insert into customer values (5000, 'bob johnson', '789 oak avenue', '555-456-7890', 'bob@example.com', 1000);
insert into customer values (6000, 'eva williams', '321 pine lane', '555-234-5678', 'eva@example.com', 2000);
insert into customer values (1000, 'david brown', '567 cedar drive', '555-876-5432', 'david@example.com', 5000);

insert into transaction values (2128292, 'credit card', 2000);
insert into transaction values (2128293, 'credit card', 2000);
insert into transaction values (1849354, 'credit card', 3000);
insert into transaction values (2128973, 'cash', 4000);
insert into transaction values (3127543, 'paypal', 5000);
insert into transaction values (3127542, 'paypal', 5000);
insert into transaction values (3127541, 'paypal', 5000);
insert into transaction values (4904532, 'paypal', 6000);
insert into transaction values (5012394, 'credit card', 1000);

insert into ticketstatus values (2128292, 'sold');
insert into ticketstatus values (2128293, 'pending');
insert into ticketstatus values (1849354, 'sold');
insert into ticketstatus values (2128973, 'sold');
insert into ticketstatus values (3127543, 'sold');
insert into ticketstatus values (3127542, 'sold');
insert into ticketstatus values (3127541, 'sold');
insert into ticketstatus values (4904532, 'sold');
insert into ticketstatus values (5012394, 'sold');

insert into ticketprice values ('ga', 1, 50);
insert into ticketprice values ('vip', 1, 150);
insert into ticketprice values ('standard', 2, 40);
insert into ticketprice values ('student', 2, 25);
insert into ticketprice values ('regular', 3, 30);

insert into ticket values (1578, 'ga', 1, 1849354);
insert into ticket values (1580, 'ga', 1, 1849354);
insert into ticket values (2908, 'vip', 1, 2128973);
insert into ticket values (2909, 'vip', 1, 2128292);
insert into ticket values (28097, 'standard', 2, 3127543);
insert into ticket values (28096, 'standard', 2, 3127542);
insert into ticket values (28095, 'standard', 2, 3127541);
insert into ticket values (14934, 'student', 2, 4904532);
insert into ticket values (389, 'regular', 3, 5012394);
insert into ticket values (390, 'regular', 3, 2128293);

insert into sponsor values (1, 'abc corporation', '123 main street', '555-123-4567', 'abc@example.com');
insert into sponsor values (2, 'xyz ltd', '456 elm road', '555-987-6543', 'xyz@example.com');
insert into sponsor values (3, 'sample corp', '789 oak avenue', '555-456-7890', 'sample@example.com');
insert into sponsor values (4, 'global solutions', '321 pine lane', '555-234-5678', 'global@example.com');
insert into sponsor values (5, 'mega corp', '567 cedar drive', '555-876-5432', 'mega@example.com');

insert into sponsorship values (1, 1, 5000);
insert into sponsorship values (1, 2, 3000);
insert into sponsorship values (2, 3, 8000);
insert into sponsorship values (3, 4, 6000);
insert into sponsorship values (4, 5, 7500);

insert into staff values (1, 'john smith', '123 main street', '555-123-4567', 'john@example.com');
insert into staff values (2, 'jane doe', '456 elm road', '555-987-6543', 'jane@example.com');
insert into staff values (3, 'robert johnson', '789 oak avenue', '555-456-7890', 'robert@example.com');
insert into staff values (4, 'sara wilson', '321 pine lane', '555-234-5678', 'sara@example.com');
insert into staff values (5, 'david brown', '567 cedar drive', '555-876-5432', 'david@example.com');

insert into assignment values (1, 1, 'coordinator');
insert into assignment values (2, 1, 'coordinator');
insert into assignment values (3, 1, 'coordinator');
insert into assignment values (4, 1, 'assistant');
insert into assignment values (5, 1, 'manager');
insert into assignment values (1, 2, 'assistant');
insert into assignment values (2, 3, 'coordinator');
insert into assignment values (1, 4, 'coordinator');
insert into assignment values (2, 4, 'coordinator');
insert into assignment values (3, 4, 'manager');
insert into assignment values (4, 4, 'assistant');
insert into assignment values (5, 4, 'manager');
insert into assignment values (4, 5, 'technician');


