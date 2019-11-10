#!/bin/sh

GREEN='\e[32m'
RED='\e[31m'
YELLOW='\e[33m'
NC='\e[39m' # No Color

cd 

echo -n "Verifica esistenza ~/TECHWEB... "
if [ -d "~/TECHWEB" ]; then
  echo "${GREEN}OK${NC}"
else
  echo "${YELLOW}Directory not found${NC}"
  echo "Clone da Github"
  cd ~
  git clone https://github.com/GiulioUmbrella/TECHWEB.git
fi

cd ~/TECHWEB
if git checkout Db_branch; then
  echo echo "${YELLOW}Moving to Db_Branch${NC}"
fi

echo "Update del repository..."
if git pull --all; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo -n "Creazione dati... "
cd Database/data_builder
if python3 build_data.py; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

cd .. #Torno alla cartella TECHWEB/Database

echo "Pulizia database (clean.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < clean.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo "Creazione tabelle (createtables.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < createtables.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo "Creazione funzioni e procedure (functions.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < functions.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi

echo "Caricamento dati (load_data.sql)... "
if mysql -h localhost -P3306 -u ${LOGNAME} -D ${LOGNAME} --local-infile=1 --password=$( cat $HOME/pwd_db-1920.txt ) --show-warnings < load_data.sql; then
  echo "${GREEN}DONE${NC}"
else
  echo "${RED}FAILED${NC}"
  exit 1
fi
