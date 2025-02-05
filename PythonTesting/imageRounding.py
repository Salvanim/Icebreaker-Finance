from PIL import Image

def roundImage(input_path, output_path, roundAmount):
    try:
        im = Image.open(input_path).convert('RGBA')
        imageOutput = Image.new(im.mode, im.size)
        imageList = list(im.getdata())
        print(imageList)
        for pixelIndex in range(len(imageList)):
            createdPixel = []
            for val in range(len(imageList[pixelIndex])):
                createdPixel.append((int(round(imageList[pixelIndex][val]/roundAmount))*roundAmount))
            imageList[pixelIndex] = tuple(createdPixel)
        imageOutput.putdata(imageList)
        imageOutput.save(output_path)
    except FileNotFoundError:
        raise FileNotFoundError(f"The file '{input_path}' was not found.")

roundImage("PythonTesting/test.jpg", "PythonTesting/testingImage.png", 12)