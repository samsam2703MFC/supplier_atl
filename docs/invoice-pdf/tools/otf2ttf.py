"""Convert CFF-flavoured OpenType (OTTO) fonts to TrueType outlines (glyf) so TCPDF can import them."""
import sys, os
from fontTools.ttLib import TTFont, newTable
from fontTools.pens.cu2quPen import Cu2QuPen
from fontTools.pens.ttGlyphPen import TTGlyphPen

MAX_ERR = 1.0  # font units; ~0.1% of the 1000 UPM em, visually lossless at print sizes


def glyphs_to_quadratic(glyph_set, max_err=MAX_ERR, reverse_direction=True):
    out = {}
    for name in glyph_set.keys():
        tt_pen = TTGlyphPen(glyph_set)
        cu2qu_pen = Cu2QuPen(tt_pen, max_err, reverse_direction=reverse_direction)
        glyph_set[name].draw(cu2qu_pen)
        out[name] = tt_pen.glyph()
    return out


def otf_to_ttf(src, dst):
    font = TTFont(src)
    assert font.sfntVersion == "OTTO" and "CFF " in font, src
    glyph_order = font.getGlyphOrder()
    font["loca"] = newTable("loca")
    font["glyf"] = glyf = newTable("glyf")
    glyf.glyphOrder = glyph_order
    glyf.glyphs = glyphs_to_quadratic(font.getGlyphSet())
    del font["CFF "]
    for t in ("VORG", "DSIG"):
        if t in font:
            del font[t]
    glyf.compile(font)
    hmtx = font["hmtx"]
    for gname, g in glyf.glyphs.items():
        if hasattr(g, "xMin"):
            hmtx[gname] = (hmtx[gname][0], g.xMin)
    font["maxp"] = maxp = newTable("maxp")
    maxp.tableVersion = 0x00010000
    maxp.maxZones = 1
    maxp.maxTwilightPoints = 0
    maxp.maxStorage = 0
    maxp.maxFunctionDefs = 0
    maxp.maxInstructionDefs = 0
    maxp.maxStackElements = 0
    maxp.maxSizeOfInstructions = 0
    maxp.maxComponentElements = max(
        (len(g.components) if hasattr(g, "components") else 0) for g in glyf.glyphs.values()
    )
    maxp.compile(font)
    post = font["post"]
    post.formatType = 2.0
    post.extraNames = []
    post.mapping = {}
    post.glyphOrder = glyph_order
    try:
        post.compile(font)
    except OverflowError:
        post.formatType = 3.0
    font.sfntVersion = "\x00\x01\x00\x00"
    font.save(dst)
    check = TTFont(dst)
    print(f"{os.path.basename(src)} -> {os.path.basename(dst)}: glyphs={len(check.getGlyphOrder())} tables={' '.join(sorted(t for t in check.keys() if t != 'GlyphOrder'))}")


if __name__ == "__main__":
    for pair in sys.argv[1:]:
        src, dst = pair.split("=")
        otf_to_ttf(src, dst)
