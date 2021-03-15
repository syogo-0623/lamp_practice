create table history (
    history_id int AUTO_INCREMENT,
    user_id int,
    create_datetime datetime,
    primary key (history_id)
);

create table detail (
    datail_id int AUTO_INCREMENT,
    history_id int,
    name VARCHAR(100),
    item_id int,
    amount int,
    price int,
    primary key(datail_id)
);