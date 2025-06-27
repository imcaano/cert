pragma solidity ^0.8.0;

contract CertificateRegistry {
    struct Certificate {
        string studentName;
        string certHash;
        uint256 timestamp;
    }

    mapping(string => Certificate) public certificates;

    function issueCertificate(string memory certId, string memory studentName, string memory certHash) public {
        certificates[certId] = Certificate(studentName, certHash, block.timestamp);
    }

    function verifyCertificate(string memory certId) public view returns (string memory, string memory, uint256) {
        Certificate memory cert = certificates[certId];
        return (cert.studentName, cert.certHash, cert.timestamp);
    }
} 