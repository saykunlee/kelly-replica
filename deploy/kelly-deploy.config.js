module.exports = {
  ftp: {
    host: '210.92.84.72',
    user: 'fms',
    password: process.env.FTP_PASSWORD || 'uny2024',
    port: 21,
    secure: false,
    remotePath: '/home/fms'
  }
};
