#
# SOURCE IMAGE
FROM phusion/baseimage

# 
MAINTAINER mail@sebastianmonzel.de

# INSTALL PACKAGES
RUN apt-get update && apt-get install -y apache2 php5 libapache2-mod-php5

#
EXPOSE 80

