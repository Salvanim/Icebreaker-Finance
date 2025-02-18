from databaseManagement import DatabaseManager
from dataVisualizationGenerator import definePlot
from imageProcessor import ImageProcessor
import sys
import inspect
from datetime import date
import switchObject as switch
import inspect

class generalProgram():
    def __init__(self, host="mysql.neit.edu", port="5500", user="capstone_202520_winteriscoming", password="Winteriscoming", database="capstone_202520_winteriscoming", data=[], columns=[], image_path="", imageSize=(), encoded_char_set="", round=0, mode='RGBA'):
        self.monthList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
        self.dataPlot = self.plot(data, columns)
        self.db = self.dbManager(host, port, user, password, database)
        self.image = self.imageProcess(image_path, imageSize, encoded_char_set, round, mode)
        self.functions = switch()
        for method in self.get_class_methods("generalProgram"):
            self.functions[method] = self(method)
        self.functions.end = "No Method Found"

    def plot(self, data, columns):
        return definePlot(data, columns)

    def dbManager(self, host, port, user, password, database):
        return DatabaseManager(host, port, user, password, database)

    def imageProcess(self, image_path="", imageSize=(), encoded_char_set="", round=0, mode='RGBA'):
        return ImageProcessor(image_path=image_path, imageSize=imageSize, encoded_char_set=encoded_char_set, round=round, mode=mode)

    def get_class_methods(cls):
        methods = inspect.getmembers(cls, predicate=lambda x: inspect.isfunction(x) or inspect.ismethod(x))
        return [name for name, _ in methods if not name.startswith("__")]

    def libraryMethodsDictionary(self):
        return {
            "DatabaseManager": self.get_class_methods(DatabaseManager),
            "definePlot": str(self.get_class_methods(definePlot) if inspect.isclass(definePlot) else "Function"),
            "ImageProcessor": str(self.get_class_methods(ImageProcessor))
        }

    def get_methods(self):
        """Extracts all function names from a given class dynamically."""
        if "generalProgram" not in globals():
            print(f"Error: Class '{"generalProgram"}' not found.")
            return

        cls = globals()["generalProgram"]
        methods = [name for name, _ in inspect.getmembers(cls, predicate=inspect.isfunction)]
        print("\n".join(methods))

    def getPlot(self, tableName):
        columns = self.db.execute(f"SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{tableName}';")
        table = self.db.viewTable(tableName)
        print(table)
        self.dataPlot.setData(table, columns)
        return self.dataPlot

    def generateBarGraph(self, debtID, userID):
        result = self.db.execute(f"SELECT amount_owed, interest_rate, min_payment FROM debt_lookup WHERE debt_id = {debtID} AND user_id = {userID};")

        if not result:
            print("No data found for the given debt ID and user ID.")
            return

        amount, rate, minPayment = result[0]
        payoffDate, months = self.calculatePayoffDate(amount, rate, minPayment)
        debtAmounts = self.getDebtAmountsEachMonth(amount, rate, minPayment, months)
        debtMonths = self.monthFromNow(months)
        return debtMonths, debtAmounts

    def getDebtAmountsEachMonth(self, amount, rate, minPayment, amountOfMonths):
        balance = amount
        debtAmounts = []
        monthlyRate = (rate / 100) / 12
        for _ in range(amountOfMonths):
            balance = balance * (1 + monthlyRate) - minPayment
            if balance < 0:
                balance = 0
            debtAmounts.append(balance)
        return debtAmounts

    def monthFromNow(self, monthDistance):
        months = []
        today = date.today()
        for i in range(monthDistance):
            year_offset = (today.month - 1 + i) // 12
            month_index = (today.month - 1 + i) % 12
            months.append(f"{self.monthList[month_index]} {today.year + year_offset}")
        return months

    def calculatePayoffDate(self, amount, rate, payment):
        balance = amount
        monthlyRate = (rate / 100) / 12
        months = 0
        today = date.today()

        if payment <= balance * monthlyRate:
            return "The monthly payment must be greater than the first month's interest. Please check your numbers and try again.", 0

        while balance > 0:
            balance = balance * (1 + monthlyRate) - payment
            months += 1
            if balance < 0:
                balance = 0

        payoffYear = today.year + (today.month - 1 + months) // 12
        payoffMonth = (today.month - 1 + months) % 12 + 1
        return f"{self.monthList[payoffMonth - 1]} {today.day}, {payoffYear}", months

    def callFunction(self, name, params):
        return self.functions(name, params, True)

    def execute(self, command):
        return self.db.execute(command)

func_name = sys.argv[1]
args = sys.argv[2:]
program = generalProgram()
program.callFunction(func_name, args)
