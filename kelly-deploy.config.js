module.exports = {
  ftp: {
    host: process.env.FTP_HOST || 'your-ftp-server.com',
    user: process.env.FTP_USER || 'your-username',
    password: process.env.FTP_PASS || 'your-password',
    port: parseInt(process.env.FTP_PORT) || 21,
    secure: process.env.FTP_SECURE === 'true' || false,
    remotePath: process.env.FTP_REMOTE_PATH || '/'
  }
};
