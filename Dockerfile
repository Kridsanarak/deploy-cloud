# เลือก base image PHP พร้อม Apache
FROM php:8.1-apache

# ติดตั้ง dependencies ที่จำเป็น
RUN apt-get update && apt-get install -y wget unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# ดาวน์โหลดและติดตั้ง phpMyAdmin
RUN wget https://files.phpmyadmin.net/phpMyAdmin/5.1.3/phpMyAdmin-5.1.3-all-languages.zip \
    && unzip phpMyAdmin-5.1.3-all-languages.zip -d /usr/share/ \
    && mv /usr/share/phpMyAdmin-5.1.3-all-languages /usr/share/phpmyadmin \
    && rm phpMyAdmin-5.1.3-all-languages.zip

# คัดลอกไฟล์ phpMyAdmin configuration (ถ้ามีการตั้งค่าไว้) 
# และตั้งค่า Virtual Host เพื่อเข้าถึง phpMyAdmin
COPY phpmyadmin.conf /etc/apache2/sites-available/phpmyadmin.conf

# เปิดใช้งาน Virtual Host และ Apache module alias
RUN a2enmod alias \
    && a2ensite phpmyadmin.conf

# ตรวจสอบว่ามีไฟล์กำหนดค่าใน /etc/apache2/conf-available/ แล้วเปิดใช้งานถ้ามี
# COPY my-httpd.conf /etc/apache2/conf-available/my-httpd.conf  # Uncomment ถ้าคุณมีไฟล์นี้
# RUN a2enconf my-httpd # Uncomment ถ้าคุณคัดลอกไฟล์นี้ไปใน container

# Copy ไฟล์ทั้งหมดไปยัง container
COPY . /var/www/html/

# ตั้งค่าการให้สิทธิ์ไฟล์ (ถ้าจำเป็น)
RUN chown -R www-data:www-data /var/www/html

# กำหนด environment variable ให้ phpMyAdmin ใช้ (เช่น ข้อมูลการเชื่อมต่อฐานข้อมูล)
ENV PMA_HOST=db
ENV PMA_PORT=3306
