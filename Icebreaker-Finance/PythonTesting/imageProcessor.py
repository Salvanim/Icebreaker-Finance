from PIL import Image
import re
import tkinter as tk
from tkinter import filedialog

class ImageProcessor:
    def __init__(self, image_path="", imageSize=(), encoded_char_set="", round=0, mode='RGBA'):
        self.mode = mode
        self.characterDictionary = self.genDictionary()
        self.valueDictionary = self.genDictionary(reverse=True)
        self.image_path = image_path

        # Open image only once
        im = Image.open(self.image_path) if self.image_path else None

        if len(imageSize) < 2 and im:
            self.width, self.height = im.size
        else:
            self.width, self.height = imageSize

        if im:
            self.pixel_values = self.roundImage(list(im.convert(self.mode).getdata()), round)
        else:
            self.pixel_values = []

        if encoded_char_set == "":
            self.encoded_char_set = self.rle_encode(self.split_char_sets_groups(self.every_pixel_convert_to_charset_string()))
        else:
            self.encoded_char_set = encoded_char_set

    def genDictionary(self, reverse=False):
        dictionary = {}
        for block in range(len(self.mode)):
            base = 19968 + block * 256
            sub_dict = {i: chr(base + i) for i in range(256)}
            dictionary[block] = sub_dict if not reverse else {v: k for k, v in sub_dict.items()}
        return dictionary

    def roundImage(self, pixelValues, roundAmount):
        if roundAmount > 0:
            return [
                tuple(min(255, (int(round(val / roundAmount) * roundAmount))) for val in pixel)
                for pixel in pixelValues
            ]
        return pixelValues

    def __str__(self):
        return self.encoded_char_set

    def convert_pixel_to_character_set(self, pixel):
        return "".join(self.characterDictionary[i][pixel[i]] for i in range(len(pixel)))

    def convert_character_set_to_pixel(self, char_set):
        return tuple(self.valueDictionary[i][char_set[i]] for i in range(len(char_set)))

    def every_pixel_convert_to_charset_string(self):
        return "".join(self.convert_pixel_to_character_set(pixel) for pixel in self.pixel_values)

    def every_charset_to_pixels(self, char_sets):
        return [self.convert_character_set_to_pixel(group) for group in self.split_char_sets_groups(char_sets)]

    def split_char_sets_groups(self, char_set_string):
        regex = r'(.{1,' + str(len(self.mode)) + r'})'
        return re.findall(regex, char_set_string)

    def rle_encode(self, arr):
        result = []
        for i in range(len(arr)):
            if not result or result[-1][0] != arr[i]:
                result.append([arr[i], 1])
            else:
                result[-1][1] += 1
        return ''.join([f"{count}{item}" if count > 1 else item for item, count in result])

    def rle_decode(self, encoded_str):
        regex = r'(\d+)?([^\d]{' + str(len(self.mode)) + r'})'
        stringNumberPairs = [(int(match[0]) if match[0] else 1, match[1]) for match in re.findall(regex, encoded_str)]
        return [char_set for num, char_set in stringNumberPairs for _ in range(num)]

    def decode(self):
        return self.every_charset_to_pixels(''.join(self.rle_decode(self.encoded_char_set)))

    def getImage(self):
        new_image = Image.new(self.mode, (self.width, self.height))
        new_image.putdata(self.decode())
        return new_image

def select_file():
    root = tk.Tk()
    root.withdraw()
    return filedialog.askopenfilename(title="Select an Image File", filetypes=[("Image files", "*.jpg;*.jpeg;*.png;*.bmp;*.gif")])

# Example of use with ImageProcessor
image_path = select_file()

# Ensure a file was selected
if image_path:
    # Create an ImageProcessor instance
    processor = ImageProcessor(image_path=image_path)

    # Print the original encoded character set
    print("Original Encoded Image Data:")
    print(processor.encoded_char_set)

    reconstructed_image = processor.getImage()

    # Save the reconstructed image
    reconstructed_image.save("reconstructed.png")

    # Display the reconstructed image
    reconstructed_image.show()

    print("Reconstructed image saved as 'reconstructed.png'.")
else:
    print("No file selected. Exiting.")
