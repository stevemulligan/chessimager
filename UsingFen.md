A [FEN string](http://en.wikipedia.org/wiki/Forsyth%E2%80%93Edwards_Notation) consists of letters, numbers, and slashes. It contains no spaces.

Lower-case letters indicate black pieces: p, r, n, b, q, and k are the pawn, rook, knight, bishop, queen, and king. Upper-case letters indicate white pieces: P, R, N, B, Q, and K. A digit indicates unoccupied squares. For example, the digit 3 indicates 3 successive unoccupied squares.

Starting with the 8th rank, you simply list the pieces and unoccupied squares in order, left-to-right. Ranks are separated by the forward slash.

A complete FEN string also includes game information such as the move number, who’s on the move, castling privileges, etc. Chess Imager, though, only uses the position information as described above.

Here’s the FEN string for the starting position:

```
rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR/
```

To generate a board image, append the FEN string to the URL pointing to the ChessImager.php script location as follows:

```
http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=
rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR/
```

The URL above generates this image:

![http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR/&nonsense=foobar.png](http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR/&nonsense=foobar.png)

Here's the URL for the position after 1. e4 c5:

```
http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=
rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR
```

Here's the resulting image:

![http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR&nonsense=foobar.png](http://www.eddins.net/steve/chess/ChessImager/ChessImager.php?fen=rnbqkbnr/pp1ppppp/8/2p5/4P3/8/PPPP1PPP/RNBQKBNR&nonsense=foobar.png)