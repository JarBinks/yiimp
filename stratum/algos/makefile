
CC=gcc

#CFLAGS=-c -g -I /usr/include/mysql
#LDFLAGS=-g

CXXFLAGS = -O2 -I.. -march=native
CFLAGS= $(CXXFLAGS) -std=gnu99
LDFLAGS=-O2 -lgmp

SOURCES=lyra2re.c lyra2v2.c Lyra2.c Sponge.c blake.c scrypt.c c11.c x11.c x13.c sha256.c keccak.c \
	x14.c x15.c nist5.c fresh.c quark.c neoscrypt.c scryptn.c qubit.c skein.c groestl.c \
	skein2.c zr5.c bmw.c luffa.c pentablake.c whirlpool.c whirlpoolx.c blakecoin.c \
	blake2.c blake2s.c \
	yescrypt.c yescrypt-opt.c sha256_Y.c \
	m7m.c magimath.cpp velvet.c \
	argon2a.c ar2/blake2b.c ar2/argon2.c ar2/ref.c ar2/cores.c ar2/ar2-scrypt-jane.c \
	hive.c pomelo.c \
	sib.c gost.c

OBJECTS=$(SOURCES:%.c=%.o) $(SOURCES:%.cpp=%.o)
OUTPUT=libalgos.a

all: $(SOURCES) $(OUTPUT)

$(OUTPUT): $(OBJECTS)
	ar rc $@ $(OBJECTS)
	touch ../stratum.cpp

.cpp.o:
	$(CC) $(CXXFLAGS) -c $< -o $@

.c.o:
	$(CC) $(CFLAGS) -c $< -o $@

#	$(CC) $(CFLAGS) -std=gnu99 -Wno-pointer-sign -Wno-pointer-to-int-cast -funroll-loops -fvariable-expansion-in-unroller -fmerge-all-constants -fbranch-target-load-optimize2 -fsched2-use-superblocks -falign-loops=16 -falign-functions=16 -falign-jumps=16 -falign-labels=16 -Ofast -flto -fuse-linker-plugin -ftree-loop-if-convert-stores -DUSE_ASM -pg $<

clean:
	rm -f *.o
	rm -f ar2/*.o

