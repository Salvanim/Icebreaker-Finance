from PIL import Image
import re
import sys

class imageComp():
    def __init__(self, image_path, round=0):
        self.image_path = image_path
        self.pixelValues = self.roundImage(self.get_pixel_values(), round)
        self.size = Image.open(image_path).convert('RGBA').size
        self.char_sets = self.every_pixel_convert_to_charset_string()
        self.encodedCharSet = self.rle_encode(self.split_char_sets_groups(self.char_sets))
        self.convertDicionary = {0:{}, 1:{}, 2:{}, 3:{}}

    def get_pixel_values(self):
        try:
            im = Image.open(self.image_path).convert('RGBA')
            return list(im.getdata())
        except FileNotFoundError:
            raise FileNotFoundError(f"The file '{self.image_path}' was not found.")
        except Exception as e:
            raise RuntimeError(f"Error opening or processing the image: {e}")
        
    def convert_pixel_to_character_set(self,pixel):
        output_character_set = ""
        i = 0
        for value in pixel:
            if 0 <= value <= 255:
                output_character_set += chr(value + (200 +(256*i)))
            else:
                raise ValueError(f"Pixel value {value} out of range (0-255).")
            i += 1
        return output_character_set

    def convert_character_set_to_pixel(self,char_set):
        pixel = []
        i = 0
        for char in char_set:
            pixel_value = ord(char) - (200 +(256*i))
            if 0 <= pixel_value <= 255:
                pixel.append(pixel_value)
            else:
                raise ValueError(f"Character {char} produces invalid pixel value {pixel_value}.")
            i += 1
        return tuple(pixel)

    def every_pixel_convert_to_charset_string(self):
        char_sets = ""
        pixel_values = self.pixelValues
        for pixel in pixel_values:
            char_sets += self.convert_pixel_to_character_set(pixel)
        return char_sets

    def every_charset_to_pixels(self,char_sets):
        pixel_values = []
        for group in self.split_char_sets_groups(char_sets):
            pixel_values.append(self.convert_character_set_to_pixel(group))
        return pixel_values

    def split_char_sets_groups(self,char_set_string):
        if len(char_set_string) % 4 != 0:
            raise ValueError("Character set string length is not a multiple of 4.")
        return [char_set_string[i:i+4] for i in range(0, len(char_set_string), 4)]

    def rle_encode(self, arr):
        result = []
        
        # Iterate through the array
        for i in range(len(arr)):
            # Get the current string
            current = arr[i]
            
            # If the result list is empty or the last string is not the same as the current string
            if not result or result[-1][0] != current:
                result.append([current, 1])
            else:
                # If the current string is the same as the last, increment the count
                result[-1][1] += 1
        
        # Convert the result into a formatted string with number in front
        rle_string = ''.join([f"{count}{item}" for item, count in result])
        
        return rle_string

    def rle_decode(self, encoded_str):
        # Extract numbers (counts) and strings separately using regex
        numbers = [int(n) for n in re.findall(r'\d+', encoded_str)]  # Find all sequences of digits
        strings = re.findall(r'[^\d]+', encoded_str)  # Find all sequences of alphabetic characters
        
        # Rebuild the list by repeating each string according to its corresponding count
        decoded_list = []
        for num, string in zip(numbers, strings):
            decoded_list.extend([string] * num)  # Repeat 'string' 'num' times
        
        return decoded_list
    
    def roundImage(self, pixelValues, roundAmount):
        if roundAmount > 0:
            imageList = pixelValues
            for pixelIndex in range(len(imageList)):
                createdPixel = []
                for val in range(len(imageList[pixelIndex])):
                    createdPixel.append((int(round(imageList[pixelIndex][val]/roundAmount))*roundAmount))
                imageList[pixelIndex] = tuple(createdPixel)
            return imageList
        else:
            return pixelValues

    def decode(self):
        decodedCharSet = ''.join(self.rle_decode(self.encodedCharSet))
        reconstructed_pixels = self.every_charset_to_pixels(decodedCharSet)
        return reconstructed_pixels

    def saveImage(self, output_path):
        imageOutput = Image.new('RGBA', self.size)
        imageOutput.putdata(self.decode())
        imageOutput.save(output_path)
        return imageOutput
'''
image = imageComp('PythonTesting/test.jpg', 0)
print(image.saveImage('PythonTesting/output.png'))
'''