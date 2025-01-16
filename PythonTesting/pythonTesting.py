from PIL import Image
import re
import sys

def get_pixel_values(image_path):
    try:
        im = Image.open(image_path).convert('RGBA')
        return list(im.getdata())
    except FileNotFoundError:
        raise FileNotFoundError(f"The file '{image_path}' was not found.")
    except Exception as e:
        raise RuntimeError(f"Error opening or processing the image: {e}")

#127744
def convert_pixel_to_character_set(pixel):
    output_character_set = ""
    i = 0
    for value in pixel:
        if 0 <= value <= 255:
            output_character_set += chr(value + (200 +(256*i)))
        else:
            raise ValueError(f"Pixel value {value} out of range (0-255).")
        i += 1
    return output_character_set

def convert_character_set_to_pixel(char_set):
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

def every_pixel_convert_to_charset_string(image_path):
    char_sets = ""
    pixel_values = get_pixel_values(image_path)
    for pixel in pixel_values:
        char_sets += convert_pixel_to_character_set(pixel)
    return char_sets

def every_charset_to_pixels(char_sets):
    pixel_values = []
    for group in split_char_sets_groups(char_sets):
        pixel_values.append(convert_character_set_to_pixel(group))
    return pixel_values

def split_char_sets_groups(char_set_string):
    if len(char_set_string) % 4 != 0:
        raise ValueError("Character set string length is not a multiple of 4.")
    return [char_set_string[i:i+4] for i in range(0, len(char_set_string), 4)]

def rle_encode(arr):
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

def rle_decode(encoded_str):
    # Extract numbers (counts) and strings separately using regex
    numbers = [int(n) for n in re.findall(r'\d+', encoded_str)]  # Find all sequences of digits
    strings = re.findall(r'[^\d]+', encoded_str)  # Find all sequences of alphabetic characters
    
    # Rebuild the list by repeating each string according to its corresponding count
    decoded_list = []
    for num, string in zip(numbers, strings):
        decoded_list.extend([string] * num)  # Repeat 'string' 'num' times
    
    return decoded_list

def main():
    image_path = 'test.jpg'

    try:
        # Extract pixel values from image
        pixels_start = get_pixel_values(image_path)

        # Convert pixels to character set string
        char_sets = every_pixel_convert_to_charset_string(image_path)
        encodedCharSet = rle_encode(split_char_sets_groups(char_sets))
        decodedCharSet = ''.join(rle_decode(encodedCharSet))
        # Convert decompressed character set string back to pixels
        reconstructed_pixels = every_charset_to_pixels(decodedCharSet)

        # Verify the reconstruction matches the original
        print("Reconstruction successful:", reconstructed_pixels == pixels_start)

    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    main()
