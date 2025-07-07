module.exports = {
  ftp: {
    host: process.env.FTP_HOST || 'kelly.unyboard.com',
    user: process.env.FTP_USER || 'kelly',
    password: process.env.FTP_PASS || 'uny2024',
    port: parseInt(process.env.FTP_PORT) || 21,
    secure: process.env.FTP_SECURE === 'true' || false,
    remotePath: process.env.FTP_REMOTE_PATH || '/home/kelly'
  }
};
