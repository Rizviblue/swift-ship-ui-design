import { useState } from "react";
import { Layout } from "@/components/courier/Layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { useToast } from "@/hooks/use-toast";

const AddCourier = () => {
  const { toast } = useToast();
  const [formData, setFormData] = useState({
    senderName: "",
    senderContact: "",
    senderAddress: "",
    receiverName: "",
    receiverContact: "",
    receiverCity: "",
    receiverAddress: "",
    trackingNumber: "",
    status: "pending"
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    toast({
      title: "Courier Added",
      description: "New courier has been added successfully.",
    });
    setFormData({
      senderName: "",
      senderContact: "",
      senderAddress: "",
      receiverName: "",
      receiverContact: "",
      receiverCity: "",
      receiverAddress: "",
      trackingNumber: "",
      status: "pending"
    });
  };

  const generateTrackingNumber = () => {
    const trackingNum = "CP" + Date.now().toString().slice(-8);
    setFormData(prev => ({ ...prev, trackingNumber: trackingNum }));
  };

  return (
    <Layout userRole="admin" userName="John Smith" userEmail="admin@courierpro.com">
      <div className="p-6">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-foreground">Add New Courier</h1>
          <p className="text-muted-foreground">Create a new courier shipment</p>
        </div>

        <Card className="max-w-4xl">
          <CardHeader>
            <CardTitle>Courier Details</CardTitle>
          </CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Sender Details */}
                <div className="space-y-4">
                  <h3 className="text-lg font-semibold text-foreground">Sender Information</h3>
                  <div className="space-y-3">
                    <div>
                      <Label htmlFor="senderName">Sender Name</Label>
                      <Input
                        id="senderName"
                        value={formData.senderName}
                        onChange={(e) => setFormData(prev => ({ ...prev, senderName: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="senderContact">Contact Number</Label>
                      <Input
                        id="senderContact"
                        type="tel"
                        value={formData.senderContact}
                        onChange={(e) => setFormData(prev => ({ ...prev, senderContact: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="senderAddress">Address</Label>
                      <Textarea
                        id="senderAddress"
                        value={formData.senderAddress}
                        onChange={(e) => setFormData(prev => ({ ...prev, senderAddress: e.target.value }))}
                        required
                      />
                    </div>
                  </div>
                </div>

                {/* Receiver Details */}
                <div className="space-y-4">
                  <h3 className="text-lg font-semibold text-foreground">Receiver Information</h3>
                  <div className="space-y-3">
                    <div>
                      <Label htmlFor="receiverName">Receiver Name</Label>
                      <Input
                        id="receiverName"
                        value={formData.receiverName}
                        onChange={(e) => setFormData(prev => ({ ...prev, receiverName: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="receiverContact">Contact Number</Label>
                      <Input
                        id="receiverContact"
                        type="tel"
                        value={formData.receiverContact}
                        onChange={(e) => setFormData(prev => ({ ...prev, receiverContact: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="receiverCity">City</Label>
                      <Input
                        id="receiverCity"
                        value={formData.receiverCity}
                        onChange={(e) => setFormData(prev => ({ ...prev, receiverCity: e.target.value }))}
                        required
                      />
                    </div>
                    <div>
                      <Label htmlFor="receiverAddress">Address</Label>
                      <Textarea
                        id="receiverAddress"
                        value={formData.receiverAddress}
                        onChange={(e) => setFormData(prev => ({ ...prev, receiverAddress: e.target.value }))}
                        required
                      />
                    </div>
                  </div>
                </div>
              </div>

              {/* Courier Details */}
              <div className="border-t pt-6">
                <h3 className="text-lg font-semibold text-foreground mb-4">Courier Details</h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div>
                    <Label htmlFor="trackingNumber">Tracking Number</Label>
                    <div className="flex space-x-2">
                      <Input
                        id="trackingNumber"
                        value={formData.trackingNumber}
                        onChange={(e) => setFormData(prev => ({ ...prev, trackingNumber: e.target.value }))}
                        required
                      />
                      <Button type="button" variant="outline" onClick={generateTrackingNumber}>
                        Generate
                      </Button>
                    </div>
                  </div>
                  <div>
                    <Label htmlFor="status">Status</Label>
                    <Select value={formData.status} onValueChange={(value) => setFormData(prev => ({ ...prev, status: value }))}>
                      <SelectTrigger>
                        <SelectValue />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="pending">Pending</SelectItem>
                        <SelectItem value="picked-up">Picked Up</SelectItem>
                        <SelectItem value="in-transit">In Transit</SelectItem>
                        <SelectItem value="delivered">Delivered</SelectItem>
                        <SelectItem value="cancelled">Cancelled</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </div>

              <div className="flex justify-end space-x-4 pt-6 border-t">
                <Button type="button" variant="outline">Cancel</Button>
                <Button type="submit">Add Courier</Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </Layout>
  );
};

export default AddCourier;