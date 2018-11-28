create table if not exists `versions`(
  `id` int(11) unsigned not null auto_increment,
  `name` varchar(255) not null,
  `created` timestamp default CURRENT_TIMESTAMP,
  primary key (id)
)
engine = InnoDB
auto_increment = 1
character set utf8
collate utf8_general_ci;