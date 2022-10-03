FROM alpine:latest

RUN apk update && \
    apk upgrade && \
    apk add mariadb mariadb-common mariadb-client && \
    /etc/init.d/mariadb setup && \
    # rc-service mariadb start && \
    rc-update add mariadb default && \
    mysql_secure_installation

CMD ["rc-service", "mariadb", "start"]