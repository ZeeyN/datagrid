create table if not exists `users` (
    `id` int(10) unsigned not null auto_increment,
    `login` varchar(255) not null,
    `password` varchar(255) not null,
    `email` varchar(255) not null,
    `f_name` varchar(255) not null,
    `l_name` varchar(255) not null,
    primary key (id)
)
engine = innodb
auto_increment = 1
character set utf8
collate utf8_general_ci;