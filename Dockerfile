FROM ubuntu
MAINTAINER mail@sebastianmonzel.de


RUN apt-get update && apt-get install -y apache2 php5 libapache2-mod-php5


EXPOSE 80

