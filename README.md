#### Run server locally
`php -S localhost:8000`

[http://localhost:8000](http://localhost:8000)

----

## Advanced Encryption Standard
The Advanced Encryption Standard (AES), also known by its original name Rijndael is a specification for the encryption of electronic data established by the U.S. National Institute of Standards and Technology (NIST) in 2001.

AES is a variant of the Rijndael block cipher developed by two Belgian cryptographers, Vincent Rijmen and Joan Daemen, who submitted a proposal to NIST during the AES selection process. 

Rijndael is a family of ciphers with different key and block sizes. For AES, NIST selected three members of the Rijndael family, each with a block size of 128 bits, but three different key lengths: 128, 192 and 256 bits.

The algorithm described by AES is a symmetric-key algorithm, meaning the same key is used for both encrypting and decrypting the data. 

### High-level description of the algorithm

#### Initial round key addition:
- AddRoundKey – each byte of the state is combined with a byte of the round key using bitwise xor.
9, 11 or 13 rounds:
- SubBytes – a non-linear substitution step where each byte is replaced with another according to a lookup table.
- ShiftRows – a transposition step where the last three rows of the state are shifted cyclically a certain number of steps.
- MixColumns – a linear mixing operation which operates on the columns of the state, combining the four bytes in each column.
- AddRoundKey

#### Final round
- SubBytes
- ShiftRows
- AddRoundKey
