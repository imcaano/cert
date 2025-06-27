require("@nomicfoundation/hardhat-toolbox");

/** @type import('hardhat/config').HardhatUserConfig */
module.exports = {
  solidity: "0.8.20",
  networks: {
    ganache: {
      url: "http://127.0.0.1:7545",
      accounts: ["0x22c9321e7b51aa45db2ada9856214d442aa6596b71e23e69285bc4d978c3a647"]
    }
  }
};
