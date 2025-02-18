from PIL import Image

def roundImage(input_path, output_path, roundAmount):
    try:
        im = Image.open(input_path).convert('RGBA')  # Ensure the image is in RGBA mode
        imageList = list(im.getdata())

        # Round pixel values while ensuring they stay within the 0-255 range
        roundedPixels = [
            tuple(min(255, max(0, int(round(val / roundAmount) * roundAmount))) for val in pixel)
            for pixel in imageList
        ]

        # Create new image and save modified pixels
        imageOutput = Image.new(im.mode, im.size)
        imageOutput.putdata(roundedPixels)
        imageOutput.save(output_path)

        print(f"Image successfully processed and saved as '{output_path}'.")

    except FileNotFoundError:
        raise FileNotFoundError(f"The file '{input_path}' was not found.")
    except Exception as e:
        print(f"An error occurred: {e}")
