module.exports = {
  ftp: {
    host: 'kelly.unyboard.com',
    user: 'kelly',
    password: process.env.FTP_PASSWORD || 'uny2024',
    port: 21,
    secure: false,
    remotePath: '/home/kelly'
  }
};
