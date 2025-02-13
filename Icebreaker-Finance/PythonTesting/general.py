from databaseManagement import DatabaseManager
from dataVisilizationGenerator import definePlot
from imageProccesor import ImageProcessor
import sys
import inspect
import switchObject

def get_class_methods(cls):
    methods = inspect.getmembers(cls, predicate=inspect.isfunction)
    return [name for name, _ in methods if name != '__init__']

def libraryMethodsDictionary():
    return {"DatabaseManager" : get_class_methods(DatabaseManager), "definePlot" : str(get_class_methods(definePlot)), "ImageProcessor" : str(get_class_methods(ImageProcessor))}

def getPlot(tableName):
    db = DatabaseManager("mysql.neit.edu","5500","capstone_202520_winteriscoming","Winteriscoming","capstone_202520_winteriscoming")
    columns = db.execute("SELECT COLUMN_NAME", "FROM INFORMATION_SCHEMA.COLUMNS", f"WHERE TABLE_NAME = {tableName};")
    table = db.viewTable(tableName)
    print(table)
    return definePlot(table, columns)

def pie(tableName, columnName, columnLables=[], title='', explode=[], autopct='%1.2f%%', colors=[], shadow=False):
    return getPlot(tableName).pie(columnName, columnLables, title, explode, autopct, colors, shadow)
    

#input = sys.stdin.read().strip()
'''
if __name__ == "__main__":
    params = sys.argv[1:]  # Exclude script name
    for param in params:
        print("[" +",".join(libraryMethodsDictionary()[params])+"]")
'''