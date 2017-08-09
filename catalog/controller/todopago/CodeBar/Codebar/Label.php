<?php

include_once('FontPhp.php');

class Label {
    const POSITION_TOP = 0;
    const POSITION_RIGHT = 1;
    const POSITION_BOTTOM = 2;
    const POSITION_LEFT = 3;

    const ALIGN_LEFT = 0;
    const ALIGN_TOP = 0;
    const ALIGN_CENTER = 1;
    const ALIGN_RIGHT = 2;
    const ALIGN_BOTTOM = 2;

    private $font;
    private $text;
    private $position;
    private $alignment;
    private $offset;
    private $spacing;
    private $rotationAngle;
    private $backgroundColor;
    private $foregroundColor;

    public function __construct($text = '', $font = null, $position = self::POSITION_BOTTOM, $alignment = self::ALIGN_CENTER) {
        $font = $font === null ? new FontPhp(5) : $font;
        $this->setFont($font);
        $this->setText($text);
        $this->setPosition($position);
        $this->setAlignment($alignment);
        $this->setSpacing(4);
        $this->setOffset(0);
        $this->setRotationAngle(0);
        $this->setBackgroundColor(new Color('white'));
        $this->setForegroundColor(new Color('black'));
    }

    public function getText() {
        return $this->font->getText();
    }

    public function setText($text) {
        $this->text = $text;
        $this->font->setText($this->text);
    }

    public function getFont() {
        return $this->font;
    }

    public function setFont($font) {
        if ($font === null) {
            throw new Exception('Font cannot be null.');
        }

        $this->font = clone $font;
        $this->font->setText($this->text);
        $this->font->setRotationAngle($this->rotationAngle);
        $this->font->setBackgroundColor($this->backgroundColor);
        $this->font->setForegroundColor($this->foregroundColor);
    }

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $position = intval($position);
        if ($position !== self::POSITION_TOP && $position !== self::POSITION_RIGHT && $position !== self::POSITION_BOTTOM && $position !== self::POSITION_LEFT) {
            throw new Exception('The text position must be one of a valid constant.');
        }

        $this->position = $position;
    }

    public function getAlignment() {
        return $this->alignment;
    }

    public function setAlignment($alignment) {
        $alignment = intval($alignment);
        if ($alignment !== self::ALIGN_LEFT && $alignment !== self::ALIGN_TOP && $alignment !== self::ALIGN_CENTER && $alignment !== self::ALIGN_RIGHT && $alignment !== self::ALIGN_BOTTOM) {
            throw new Exception('The text alignment must be one of a valid constant.');
        }

        $this->alignment = $alignment;
    }

    public function getOffset() {
        return $this->offset;
    }

    public function setOffset($offset) {
        $this->offset = intval($offset);
    }

    public function getSpacing() {
        return $this->spacing;
    }

    public function setSpacing($spacing) {
        $this->spacing = max(0, intval($spacing));
    }

    public function getRotationAngle() {
        return $this->font->getRotationAngle();
    }

    public function setRotationAngle($rotationAngle) {
        $this->rotationAngle = intval($rotationAngle);
        $this->font->setRotationAngle($this->rotationAngle);
    }

    public function getBackgroundColor() {
        return $this->backgroundColor;
    }

    public function setBackgroundColor($backgroundColor) {
        $this->backgroundColor = $backgroundColor;
        $this->font->setBackgroundColor($this->backgroundColor);
    }

    public function getForegroundColor() {
        return $this->font->getForegroundColor();
    }

    public function setForegroundColor($foregroundColor) {
        $this->foregroundColor = $foregroundColor;
        $this->font->setForegroundColor($this->foregroundColor);
    }

    public function getDimension() {
        $w = 0;
        $h = 0;

        $dimension = $this->font->getDimension();
        $w = $dimension[0];
        $h = $dimension[1];

        if ($this->position === self::POSITION_TOP || $this->position === self::POSITION_BOTTOM) {
            $h += $this->spacing;
            $w += max(0, $this->offset);
        } else {
            $w += $this->spacing;
            $h += max(0, $this->offset);
        }

        return array($w, $h);
    }

    public function draw($im, $x1, $y1, $x2, $y2) {
        $x = 0;
        $y = 0;

        $fontDimension = $this->font->getDimension();

        if ($this->position === self::POSITION_TOP || $this->position === self::POSITION_BOTTOM) {
            if ($this->position === self::POSITION_TOP) {
                $y = $y1 - $this->spacing - $fontDimension[1];
            } elseif ($this->position === self::POSITION_BOTTOM) {
                $y = $y2 + $this->spacing;
            }

            if ($this->alignment === self::ALIGN_CENTER) {
                $x = ($x2 - $x1) / 2 + $x1 - $fontDimension[0] / 2 + $this->offset;
            } elseif ($this->alignment === self::ALIGN_LEFT)  {
                $x = $x1 + $this->offset;
            } else {
                $x = $x2 + $this->offset - $fontDimension[0];
            }
        } else {
            if ($this->position === self::POSITION_LEFT) {
                $x = $x1 - $this->spacing - $fontDimension[0];
            } elseif ($this->position === self::POSITION_RIGHT) {
                $x = $x2 + $this->spacing;
            }

            if ($this->alignment === self::ALIGN_CENTER) {
                $y = ($y2 - $y1) / 2 + $y1 - $fontDimension[1] / 2 + $this->offset;
            } elseif ($this->alignment === self::ALIGN_TOP)  {
                $y = $y1 + $this->offset;
            } else {
                $y = $y2 + $this->offset - $fontDimension[1];
            }
        }

        $this->font->setText($this->text);
        $this->font->draw($im, $x, $y);
    }
}
