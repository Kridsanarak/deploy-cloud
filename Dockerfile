# เลือก base image PHP พร้อม Apache
FROM php:8.1-apache

# ติดตั้ง dependencies ที่จำเป็น
RUN apt-get update && apt-get install -y wget unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev default-mysql-client \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ดาวน์โหลดและติดตั้ง phpMyAdmin
RUN wget https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-all-languages.zip \
    && unzip phpMyAdmin-latest-all-languages.zip -d /usr/share/ \
    && mv /usr/share/phpMyAdmin-*-all-languages /usr/share/phpmyadmin \
    && rm phpMyAdmin-latest-all-languages.zip

# คัดลอกไฟล์ phpMyAdmin configuration
COPY phpmyadmin.conf /etc/apache2/sites-available/phpmyadmin.conf

# เปิดใช้งาน Virtual Host และ Apache module alias
RUN a2enmod alias \
    && a2ensite phpmyadmin.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copy ไฟล์ทั้งหมดไปยัง container
COPY . /var/www/html/realproject

# ตั้งค่าการให้สิทธิ์ไฟล์ (ถ้าจำเป็น)
RUN chown -R www-data:www-data /var/www/html/realproject /usr/share/phpmyadmin

# กำหนด environment variable ให้ phpMyAdmin ใช้
ENV PMA_HOST=db
ENV PMA_PORT=3306

# รัน Apache ใน foreground
CMD ["apache2-foreground"]
