from databaseManagement import DatabaseManager
from dataVisilizationGenerator import definePlot
from imageProccesor import ImageProcessor
import sys
import inspect

def get_class_methods(cls):
    methods = inspect.getmembers(cls, predicate=inspect.isfunction)
    return [name for name, _ in methods if name != '__init__']

def libraryMethodsDictionary():
    return {"DatabaseManager" : get_class_methods(DatabaseManager), "definePlot" : str(get_class_methods(definePlot)), "ImageProcessor" : str(get_class_methods(ImageProcessor))}

#input = sys.stdin.read().strip()
db = DatabaseManager("mysql.neit.edu","5500","capstone_202520_winteriscoming","Winteriscoming","capstone_202520_winteriscoming")

if __name__ == "__main__":
    params = sys.argv[1:]  # Exclude script name
    for param in params:
        print("[" +",".join(libraryMethodsDictionary()[params])+"]")