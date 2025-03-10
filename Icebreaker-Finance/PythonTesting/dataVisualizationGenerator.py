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
        self.fig, ax = plt.subplots(figsize=(12, 7))  # Slightly larger figure for better spacing
        ax.plot(self.dataframe[xColumnName], self.dataframe[yColumnName],
                color=color, linewidth=linewidth, marker=marker, zorder=5)

        ax.set_title(title, pad=20)
        ax.set_xlabel(xlabel, labelpad=15)
        ax.set_ylabel(ylabel, labelpad=15)

        # Calculate dynamic vertical offset (3% of data range)
        y_min = self.dataframe[yColumnName].min()
        y_max = self.dataframe[yColumnName].max()
        y_range = y_max - y_min
        y_offset = y_range * 0.03 if y_range > 0 else 1

        # Annotate points with intelligent positioning
        for i, row in self.dataframe.iterrows():
            # Position label above the point with horizontal centering
            ax.text(row[xColumnName],
                    row[yColumnName] + y_offset,
                    f"${row[yColumnName]:.2f}",
                    ha='center',
                    va='bottom',
                    fontsize=9,
                    color='black',
                    bbox=dict(facecolor='white', alpha=0.8, edgecolor='none', pad=1),
                    zorder=10)

        # Configure x-axis
        ax.set_xticks(self.dataframe[xColumnName])
        plt.xticks(rotation=35, ha='right')  # Rotate dates diagonally
        ax.xaxis.set_major_formatter(DateFormatter('%Y-%m-%d'))

        # Add gridlines for better readability
        ax.grid(True, linestyle='--', alpha=0.7, zorder=0)

        # Ensure proper spacing
        plt.tight_layout()
        plt.subplots_adjust(top=0.9)  # Add extra space at the top

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
