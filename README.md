# QR Code Generator API

A simple REST API for generating QR codes from URLs, containerized with Docker.

## Features

- 🔐 Basic Authentication
- 🎨 Customizable QR code size
- 🐳 Docker containerized
- 📦 Multiple output formats (image/base64)
- ✅ URL validation

## Quick Start

### Using Docker Compose

1. Clone the repository:
```bash
   git clone https://github.com/rodmontgt/qr-code-api.git
   cd qr-code-api
```

2. Copy and configure environment variables:
```bash
   cp .env.example .env
   # Edit .env and set your credentials
```

3. Start the container:
```bash
   docker-compose up -d
```

4. The API will be available at `http://localhost:8080`

### Using Docker
```bash
docker build -t qr-code-api .
docker run -d -p 8080:80 \
  -e API_USERNAME=admin \
  -e API_PASSWORD=your_password \
  --name qr-api \
  qr-code-api
```

## API Usage

### Generate QR Code (Image)
```bash
curl -u admin:your_password \
  "http://localhost:8080/?url=https://www.google.com&size=300" \
  --output qrcode.png
```

### Generate QR Code (Base64 JSON)
```bash
curl -u admin:your_password \
  "http://localhost:8080/?url=https://www.google.com&size=300&format=base64"
```

### Parameters

- `url` (required): The URL to encode in the QR code
- `size` (optional): QR code size in pixels (100-1000, default: 300)
- `format` (optional): Output format - `image` or `base64` (default: image)

## Environment Variables

- `API_USERNAME`: Basic auth username (default: admin)
- `API_PASSWORD`: Basic auth password (default: changeme)

## Security Notes

⚠️ **Important**: Always change the default credentials in production!

1. Set strong credentials in your `.env` file
2. Use HTTPS in production
3. Consider adding rate limiting for production use

## License

MIT License