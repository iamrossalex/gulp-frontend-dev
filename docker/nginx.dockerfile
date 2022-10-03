FROM alpine

RUN cd /root && apk update && apk upgrade && \
	apk add linux-headers curl wget gcc g++ libunwind-dev go make cmake libxml2-dev libxslt-dev perl git perl-dev pcre-dev zlib-dev libgd libaio openssl-dev libmcrypt-dev freetype libpng libjpeg-turbo freetype-dev libpng-dev libjpeg-turbo-dev && \
	wget -O nginx-quic.tar.gz https://hg.nginx.org/nginx-quic/archive/tip.tar.gz && \
	tar zxf nginx-quic.tar.gz && \
	wget https://github.com/PhilipHazel/pcre2/releases/download/pcre2-10.39/pcre2-10.39.tar.gz && \
	tar zxf pcre2-10.39.tar.gz && \
	cd /root/pcre2-10.39 && ./configure && make && make install && cd /root && \
	git clone https://boringssl.googlesource.com/boringssl && \
	cd /root/boringssl/ && cmake . && make && cd /root && \
	git clone https://github.com/libgd/libgd.git && \
	cd /root/libgd/ && cmake . && make && make install && cd /root && \
	git clone https://github.com/google/ngx_brotli.git && \
	cd /root/ngx_brotli && git submodule update --init && cd /root && \
	cd /root/nginx-quic-* && \
	auto/configure --prefix=/etc/nginx --sbin-path=/usr/sbin/nginx --modules-path=/usr/lib/nginx/modules --conf-path=/etc/nginx/nginx.conf --error-log-path=/var/log/nginx/error.log --http-log-path=/var/log/nginx/access.log --pid-path=/var/run/nginx.pid --lock-path=/var/run/nginx.lock --http-client-body-temp-path=/var/cache/nginx/client_temp --http-proxy-temp-path=/var/cache/nginx/proxy_temp --http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp --http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp --http-scgi-temp-path=/var/cache/nginx/scgi_temp --user=nginx --group=nginx --with-http_ssl_module --with-http_realip_module --with-http_addition_module --with-http_sub_module --with-http_dav_module --with-http_flv_module --with-http_mp4_module --with-http_gunzip_module --with-http_gzip_static_module --with-http_random_index_module --with-http_secure_link_module --with-http_stub_status_module --with-http_auth_request_module --with-http_xslt_module=dynamic --with-http_image_filter_module=dynamic --with-http_perl_module=dynamic --with-threads --with-stream --with-stream_ssl_module --with-http_slice_module --with-mail --with-mail_ssl_module --with-file-aio --with-http_v2_module --with-cc-opt='-g -O2 -fstack-protector-strong -Wformat -Werror=format-security -Wp,-D_FORTIFY_SOURCE=2 -DTCP_FASTOPEN=23' --with-ld-opt='-Wl,-Bsymbolic-functions -Wl,-z,relro -Wl,--as-needed' --with-debug --with-http_v3_module --with-cc-opt="-I../boringssl/include" --with-ld-opt="-L../boringssl/ssl -L../boringssl/crypto" --add-module=/root/ngx_brotli && \
	make && \
	make install && \
    adduser -DHs /bin/false nginx && \
    mkdir -p /var/cache/nginx/client_temp && \
	echo "daemon off;" >> /etc/nginx/nginx.conf

# ADD nginx.conf /etc/nginx/
EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/nginx"]

# docker build -t wacdis/nginx:latest -f ./nginx.dockerfile .
