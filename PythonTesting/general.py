from databaseManagement import DatabaseManager
from dataVisilizationGenerator import definePlot
from imageProccesor import ImageProcessor

db = DatabaseManager("mysql.neit.edu","5500","capstone_202520_winteriscoming","Winteriscoming","capstone_202520_winteriscoming")
db.resetPrimary("users", 0)
print(db.viewTable('users'))