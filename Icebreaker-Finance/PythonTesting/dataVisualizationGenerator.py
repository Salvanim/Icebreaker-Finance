import sys
import json
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import pandas as pd
import io
import base64
from matplotlib.dates import DateFormatter

class DefinePlot:
    def __init__(self, data=[], columns=[]):
        self.data = data
        self.columns = columns
        self.dataframe = pd.DataFrame(data, columns=columns)
        # Ensure Amount is numeric and Date is datetime
        self.dataframe["Amount"] = pd.to_numeric(self.dataframe["Amount"], errors='coerce')
        self.dataframe["Date"] = pd.to_datetime(self.dataframe["Date"], errors='coerce')
        self.fig = None

    def getImageBase64(self):
        if self.fig:
            buf = io.BytesIO()
            self.fig.savefig(buf, format='png')
            buf.seek(0)
            return base64.b64encode(buf.getvalue()).decode('utf-8')
        else:
            return "No plot has been created yet."

    def line(self, xColumnName, yColumnName, title='', xlabel='', ylabel='', color='blue', linewidth=2, marker='o'):
        self.fig, ax = plt.subplots(figsize=(10, 6))  # Increase figure size for better label visibility
        ax.plot(self.dataframe[xColumnName], self.dataframe[yColumnName], color=color, linewidth=linewidth, marker=marker)
        ax.set_title(title)
        ax.set_xlabel(xlabel)
        ax.set_ylabel(ylabel)

        # Annotate each point with its value
        for i, row in self.dataframe.iterrows():
            ax.text(row[xColumnName], row[yColumnName], f"{row[yColumnName]:.2f}",
                    ha='left', va='bottom', fontsize=10, color='black')

        # Set the x-ticks to only the provided dates
        ax.set_xticks(self.dataframe[xColumnName])
        plt.xticks(rotation=20)

        # Set the x-axis label format for the dates
        ax.xaxis.set_major_formatter(DateFormatter('%Y-%m-%d'))

        # Adjust layout to prevent overlap
        plt.tight_layout()

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Error: No input provided")
        sys.exit(1)

    try:
        # Get and validate input
        raw_input = sys.argv[1]
        input_data = raw_input
        individualRows = input_data.split("$")
        individualRows = individualRows[:len(individualRows)-1]
        finalData = []
        for row in individualRows:
            finalData.append(row.split(","))

        # Rest of plotting logic
        plotter = DefinePlot(finalData, ["Date", "Amount"])
        plotter.line("Date", "Amount")
        plot_method = getattr(plotter, "line")
        plotArguments = {
            "xColumnName": "Date",
            "yColumnName": "Amount",
            "title": "Payment History",
            "xlabel": "Date",
            "ylabel": "Amount ($)",
            "color": "blue",
            "linewidth": 2,
            "marker": "o"
        }
        plot_method(**plotArguments)
        print(plotter.getImageBase64())

    except Exception as e:
        print(f"Error: {e}")
        sys.stderr.write(f"FULL ERROR: {str(e)}\n")
        sys.exit(1)
