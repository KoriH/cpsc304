CREATE TABLE users (
	Username varchar(20) NOT NULL,
	Password varchar(40) NOT NULL,
	Primary Key(Username)
);

CREATE TABLE Cooler_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  Fanspeed INT NOT NULL
);

CREATE TABLE Cooler_R2 (
  Fanspeed INT PRIMARY KEY,
  Wattage INT NOT NULL,
  Cost DECIMAL(10, 2) NOT NULL
);



CREATE TABLE CPU_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  hasStockCooler INT NOT NULL,
  Cost DECIMAL(10, 2) NOT NULL,
  Wattage INT NOT NULL
);


CREATE TABLE CPU_R2 (
  Name VARCHAR(255) PRIMARY KEY,
  BaseClock DECIMAL(5, 2) NOT NULL,
  CoreCount INT NOT NULL
);



CREATE TABLE Motherboard_R2 (
  Name VARCHAR(255) PRIMARY KEY,
  MemorySlots INT NOT NULL,
  MaxMemory INT NOT NULL
);

CREATE TABLE Motherboard_R3 (
  Name VARCHAR(255) PRIMARY KEY,
  SocketType VARCHAR(50) NOT NULL,
  FormFactor VARCHAR(50) NOT NULL,
  Cost DECIMAL(10, 2) NOT NULL
);

CREATE TABLE Motherboard_R4 (
  FormFactor VARCHAR(50) PRIMARY KEY,
  MemorySlots INT NOT NULL
);



CREATE TABLE PowerSupply_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  Rating VARCHAR(50) NOT NULL,
  Wattage INT NOT NULL
);

CREATE TABLE PowerSupply_R2 (
  Rating VARCHAR(50),
  Wattage INT NOT NULL,
  Cost DECIMAL(10, 2) NOT NULL,
  primary key(Rating, Wattage)
);


CREATE TABLE VideoCard_R2 (
  Memory INT PRIMARY KEY,
  Cost DECIMAL(10, 2) NOT NULL
);

CREATE TABLE VideoCard_R3 (
  Name VARCHAR(255) PRIMARY KEY,
  Memory INT NOT NULL,
  Wattage INT NOT NULL,
  MemoryType VARCHAR(50) NOT NULL
);

CREATE TABLE VideoCard_R4 (
  Name VARCHAR(50) PRIMARY KEY,
  Brand VARCHAR(255) NOT NULL
);



CREATE TABLE Memory_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  Type VARCHAR(50) NOT NULL,
  Amount INT NOT NULL,
  ClockSpeed INT NOT NULL
);

CREATE TABLE Memory_R2 (
  Type VARCHAR(50) NOT NULL,
  Amount INT NOT NULL,
  ClockSpeed INT NOT NULL,
  Cost DECIMAL(10, 2) NOT NULL,
  PRIMARY KEY(Type, Amount, Clockspeed)
);



CREATE TABLE SSD_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  Capacity INT NOT NULL,
  Type VARCHAR(255)
);

CREATE TABLE SSD_R2 (
  Capacity INT PRIMARY KEY,
  Cost DECIMAL(10, 2) NOT NULL
);

CREATE TABLE HDD_R1 (
  Name VARCHAR(255) PRIMARY KEY,
  Capacity INT NOT NULL,
  RPM INT NOT NULL
);

CREATE TABLE HDD_R2 (
  Capacity INT PRIMARY KEY,
  Cost DECIMAL(10, 2) NOT NULL
);



CREATE TABLE Case_R1 (
  Cost DECIMAL(10, 2) NOT NULL,
  Name VARCHAR(255) PRIMARY KEY,
  Type VARCHAR(50) NOT NULL
);

CREATE TABLE Case_R2 (
  Type VARCHAR(50) PRIMARY KEY,
  FormFactor VARCHAR(50) NOT NULL
);



CREATE TABLE configuration (
    Username varchar(20) NOT NULL,
    ConfigurationName varchar(20) NOT NULL,
    Cooler varchar(255),
    CPU varchar(255),
    Motherboard varchar(255),
    PowerSupply varchar(255),
    VideoCard varchar(255),
    Memory varchar(255),
    SSD varchar(255),
    HDD varchar(255),
    CaseName varchar(255),
    PRIMARY KEY(Username, ConfigurationName),
    FOREIGN KEY(Username) References users(Username) ON DELETE CASCADE,
    FOREIGN KEY(Cooler) References Cooler_R1(Name),
    FOREIGN KEY(CPU) References CPU_R1(Name),
    FOREIGN KEY(Motherboard) References Motherboard_R2(Name),
    FOREIGN KEY(PowerSupply) References PowerSupply_R1(Name),
    FOREIGN KEY(VideoCard) References VideoCard_R3(Name),
    FOREIGN KEY(Memory) References Memory_R1(Name),
    FOREIGN KEY(SSD) References SSD_R1(Name),
    FOREIGN KEY(HDD) References HDD_R1(Name),
    FOREIGN KEY(CaseName) References Case_R1(Name)
);








INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('be quiet! Dark Rock Pro 4', 1500);

INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('Noctua NH-U12S chromax.black', 1500);

INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('NZXT Kraken X73', 2000);

INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('ARCTIC Liquid Freezer II 360', 2000);

INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('Thermalright Assassin X 120 Refined SE', 1550);

INSERT INTO Cooler_R1 (Name, Fanspeed)
VALUES ('ID-COOLING SE-214-XT', 1550);

INSERT INTO Cooler_R2 (Fanspeed, Wattage, Cost)
VALUES (1500, 10, 89.99);

INSERT INTO Cooler_R2 (Fanspeed, Wattage, Cost)
VALUES (2000, 20, 149.99);

INSERT INTO Cooler_R2 (Fanspeed, Wattage, Cost)
VALUES (1550, 10, 19.99);


INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('Intel Core i9-11900K', 1, 499.99, 95);

INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('AMD Ryzen 7 5800X', 0, 449.99, 105);

INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('Intel Core i5-12600K', 1, 269.99, 125);

INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('AMD Ryzen 9 5950X', 0, 799.99, 105);

INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('Intel Core i7-10700K', 1, 399.99, 125);

INSERT INTO CPU_R1 (Name, hasStockCooler, Cost, Wattage)
VALUES ('AMD Ryzen 5 7600X', 0, 299.99, 65);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('Intel Core i9-11900K', 3.6, 8);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('AMD Ryzen 7 5800X', 3.8, 8);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('Intel Core i5-12600K', 3.9, 6);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('AMD Ryzen 9 5950X', 3.4, 16);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('Intel Core i7-10700K', 3.8, 8);

INSERT INTO CPU_R2 (Name, BaseClock, CoreCount)
VALUES ('AMD Ryzen 5 7600X', 3.7, 6);


INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('ASUS ROG Strix X570-E Gaming', 4, 128);

INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('GIGABYTE B550 AORUS PRO', 4, 128);

INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('MSI MAG B460 TOMAHAWK', 4, 128);

INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('ASRock B450M PRO4', 4, 64);

INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('ASUS PRIME Z590-A', 4, 128);

INSERT INTO Motherboard_R2 (Name, MemorySlots, MaxMemory)
VALUES ('MSI MPG X570 GAMING PLUS', 4, 128);


INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('ASUS ROG Strix X570-E Gaming', 'AM4', 'ATX', 299.99);

INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('GIGABYTE B550 AORUS PRO', 'AM4', 'ATX', 179.99);

INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('MSI MAG B460 TOMAHAWK', 'LGA1200', 'ATX', 129.99);

INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('ASRock B450M PRO4', 'AM4', 'Micro ATX', 89.99);

INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('ASUS PRIME Z590-A', 'LGA1200', 'ATX', 249.99);

INSERT INTO Motherboard_R3 (Name, SocketType, FormFactor, Cost)
VALUES ('MSI MPG X570 GAMING PLUS', 'AM4', 'ATX', 169.99);

INSERT INTO Motherboard_R4 (FormFactor, MemorySlots)
VALUES ('ATX', 4);

INSERT INTO Motherboard_R4 (FormFactor, MemorySlots)
VALUES ('Micro ATX', 2);


INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('EVGA SuperNOVA 750 G5', '80 Plus Gold', 750);

INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('Corsair RM750x', '80 Plus Gold', 750);

INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('Seasonic Focus GX-650', '80 Plus Gold', 650);

INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('NZXT C850', '80 Plus Platinum', 850);

INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('Thermaltake Toughpower Grand RGB 750W', '80 Plus Gold', 750);

INSERT INTO PowerSupply_R1 (Name, Rating, Wattage)
VALUES ('Cooler Master MWE Gold 650 V2', '80 Plus Gold', 650);


INSERT INTO PowerSupply_R2 (Rating, Wattage, Cost)
VALUES ('80 Plus Gold', 750, 129.99);

INSERT INTO PowerSupply_R2 (Rating, Wattage, Cost)
VALUES ('80 Plus Platinum', 850, 249.99);

INSERT INTO PowerSupply_R2 (Rating, Wattage, Cost)
VALUES ('80 Plus Gold', 650, 119.99);

INSERT INTO PowerSupply_R2 (Rating, Wattage, Cost)
VALUES ('80 Plus Bronze', 550, 89.99);

INSERT INTO PowerSupply_R2 (Rating, Wattage, Cost)
VALUES ('80 Plus Bronze', 450, 69.99);


INSERT INTO VideoCard_R2 (Memory, Cost)
VALUES (10, 699.99);

INSERT INTO VideoCard_R2 (Memory, Cost)
VALUES (16, 649.99);

INSERT INTO VideoCard_R2 (Memory, Cost)
VALUES (12, 499.99);

INSERT INTO VideoCard_R2 (Memory, Cost)
VALUES (8, 399.99);

INSERT INTO VideoCard_R2 (Memory, Cost)
VALUES (24, 1999.99);


INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('NVIDIA GeForce RTX 3080', 10, 250, 'GDDR6X');

INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('AMD Radeon RX 6800 XT', 16, 250, 'GDDR6');

INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('NVIDIA GeForce RTX 4070 Ti', 12, 285, 'GDDR6X');

INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('AMD Radeon RX 6700 XT', 12, 250, 'GDDR6');

INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('NVIDIA GeForce RTX 3060 Ti', 8, 250, 'GDDR6');

INSERT INTO VideoCard_R3 (Name, Memory, Wattage, MemoryType)
VALUES ('NVIDIA GeForce RTX 4090', 24, 450, 'GDDR6X');


INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('NVIDIA GeForce RTX 3080', 'NVIDIA');

INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('AMD Radeon RX 6800 XT', 'AMD');

INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('NVIDIA GeForce RTX 4070 Ti', 'NVIDIA');

INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('AMD Radeon RX 6700 XT', 'AMD');

INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('NVIDIA GeForce RTX 3060 Ti', 'NVIDIA');

INSERT INTO VideoCard_R4 (Name, Brand)
VALUES ('NVIDIA GeForce RTX 4090', 'NVIDIA');


INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('Corsair Vengeance LPX', 'DDR4', 16, 3200);

INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('G.Skill Trident Z RGB', 'DDR4', 16, 3600);

INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('Crucial Ballistix', 'DDR4', 16, 3200);

INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('G.Skill Flare X 5', 'DDR5', 32, 6000);

INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('Team T-Force Delta RGB', 'DDR4', 16, 3200);

INSERT INTO Memory_R1 (Name, Type, Amount, ClockSpeed)
VALUES ('Corsair Vengeance 32 GB', 'DDR5', 32, 6000);


INSERT INTO Memory_R2 (Type, Amount, ClockSpeed, Cost)
VALUES ('DDR4', 16, 3200, 79.99);

INSERT INTO Memory_R2 (Type, Amount, ClockSpeed, Cost)
VALUES ('DDR4', 16, 3600, 99.99);

INSERT INTO Memory_R2 (Type, Amount, ClockSpeed, Cost)
VALUES ('DDR5', 32, 6000, 99.99);


INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Samsung 970 EVO Plus', 1000, 'M.2 PCIe 3.0 X4');

INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Crucial MX500', 2000, 'SATA 6.0Gb/s');

INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Samsung 980 Pro', 1000, 'M.2 PCIe 3.0 X4');

INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Kingston NV2', 1000, 'M.2 PCIe 4.0 X4');

INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Crucial P5 Plus', 2000, 'M.2 PCIe 4.0 X4');

INSERT INTO SSD_R1 (Name, Capacity, Type)
VALUES ('Sabrent Rocket 4 Plus', 8000, 'M.2 PCIe 4.0 X4');


INSERT INTO SSD_R2 (Capacity, Cost)
VALUES (1000, 59.99);

INSERT INTO SSD_R2 (Capacity, Cost)
VALUES (2000, 84.99);

INSERT INTO SSD_R2 (Capacity, Cost)
VALUES (8000, 999.99);


INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Western Digital Caviar Blue', 1000, 7200);

INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Western Digital Blue', 2000, 7200);

INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Toshiba DT01ACA100', 1000, 7200);

INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Seagate Barracuda Compute', 2000, 7200);

INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Seagate Barracuda', 4000, 5400);

INSERT INTO HDD_R1 (Name, Capacity, RPM)
VALUES ('Seagate IronWolf Pro NAS', 22000, 7200);


INSERT INTO HDD_R2 (Capacity, Cost)
VALUES (1000, 34.99);

INSERT INTO HDD_R2 (Capacity, Cost)
VALUES (2000, 49.99);

INSERT INTO HDD_R2 (Capacity, Cost)
VALUES (4000, 59.99);

INSERT INTO HDD_R2 (Capacity, Cost)
VALUES (22000, 352.99);


INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (99.99, 'NZXT H710i', 'ATX Mid Tower');

INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (79.99, 'Fractal Design Meshify C', 'ATX Mid Tower');

INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (149.99, 'Corsair Obsidian 500D', 'ATX Full Tower');

INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (69.99, 'Phanteks Eclipse P300', 'ATX Mid Tower');

INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (89.99, 'Cooler Master MasterBox MB511', 'ATX Mid Tower');

INSERT INTO Case_R1 (Cost, Name, Type)
VALUES (59.99, 'Thermaltake Versa H17', 'Micro ATX');


INSERT INTO Case_R2 (Type, FormFactor)
VALUES ('ATX Mid Tower', 'ATX');

INSERT INTO Case_R2 (Type, FormFactor)
VALUES ('ATX Full Tower', 'ATX');

INSERT INTO Case_R2 (Type, FormFactor)
VALUES ('Micro ATX', 'Micro ATX');



INSERT INTO users (Username, Password)
VALUES ('juck123', 'password123');

INSERT INTO users (Username, Password)
VALUES ('meowmeow', 'abcde456');

INSERT INTO users (Username, Password)
VALUES ('ilovesql', 'qwerty789');

INSERT INTO users (Username, Password)
VALUES ('jeff', 'passpass');

INSERT INTO users (Username, Password)
VALUES ('mike23', 'hello123');

INSERT INTO users (Username, Password)
VALUES ('mightygiraffe', 'test456');


INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
VALUES ('juck123', 'Gaming Setup', 'be quiet! Dark Rock Pro 4', 'Intel Core i9-11900K', 'ASUS PRIME Z590-A', 'EVGA SuperNOVA 750 G5', 'NVIDIA GeForce RTX 3080', 'Corsair Vengeance LPX', 'Samsung 970 EVO Plus', 'Western Digital Caviar Blue', 'NZXT H710i');

INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
VALUES ('meowmeow', 'Workstation', 'Noctua NH-U12S chromax.black', 'AMD Ryzen 7 5800X', 'GIGABYTE B550 AORUS PRO', 'Corsair RM750x', 'AMD Radeon RX 6800 XT', 'G.Skill Trident Z RGB', 'Crucial MX500', 'Toshiba DT01ACA100', 'Fractal Design Meshify C');

INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
VALUES ('ilovesql', 'Streaming Rig', 'NZXT Kraken X73', 'Intel Core i5-12600K', 'MSI MAG B460 TOMAHAWK', 'Seasonic Focus GX-650', 'NVIDIA GeForce RTX 4070 Ti', 'Crucial Ballistix', 'Samsung 980 Pro', 'Seagate Barracuda Compute', 'Corsair Obsidian 500D');

INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
VALUES ('jeff', 'Home Office', 'ARCTIC Liquid Freezer II 360', 'AMD Ryzen 9 5950X', 'ASRock B450M PRO4', 'NZXT C850', 'AMD Radeon RX 6700 XT', 'G.Skill Flare X 5', 'Kingston NV2', 'Seagate Barracuda', 'Phanteks Eclipse P300');

INSERT INTO configuration (Username, ConfigurationName, Cooler, CPU, Motherboard, PowerSupply, VideoCard, Memory, SSD, HDD, CaseName)
VALUES ('mightygiraffe', 'Casual Gaming', 'Thermalright Assassin X 120 Refined SE', 'Intel Core i7-10700K', 'ASUS PRIME Z590-A', 'Thermaltake Toughpower Grand RGB 750W', 'NVIDIA GeForce RTX 3060 Ti', 'Corsair Vengeance 32 GB', 'Sabrent Rocket 4 Plus', 'Seagate IronWolf Pro NAS', 'Cooler Master MasterBox MB511');
